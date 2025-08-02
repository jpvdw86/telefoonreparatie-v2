<?php

namespace App\Command;

use App\Entity\TeambaseInvoice;
use App\Service\TeambaseCsvHelper;
use App\Service\TeambaseHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Twig\Environment;

#[AsCommand('teambase:csv')]
class ImportInvoicesCommand extends Command
{
    protected array $readyToSend = [];

    public function __construct(
        private readonly TeambaseHelper $teambaseHelper,
        private readonly TeambaseCsvHelper $teambaseCsvHelper,
        private readonly EntityManagerInterface $entityManager,
        private readonly MailerInterface $mailer
    )
    {
        parent::__construct();

    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $end = new \DateTime('last day of previous month');
        $start = clone $end;
        $start->modify('-3 MONTH');
        $start->setDate($start->format('Y'),$start->format('m'),1);
        $start->setTime(0, 0, 0);
        $end->setTime(23, 59, 59);


        if(in_array($end->format('m'), ['03','06','09','12'])) {

            $invoices = $this->teambaseHelper->getInvoiceBetween($start, $end);
            if($invoices) {
                echo "Total invoices " . count($invoices) . PHP_EOL;
                foreach ($invoices as $invoice) {
                    if ($this->addToDatabase($invoice)) {
                        echo "+";
                    }
                }
            }

            $this->generateCsv();

            $csvFile = $this->teambaseCsvHelper->getCsV("{$start->format('Ymd')}_{$end->format('Ymd')}");
            $this->sendMail($csvFile);
        }
    }

    public function sendMail(string $csvFile): void
    {
        $message = (new TemplatedEmail())
            ->subject('Facturen CSV - De telefoon reparatie winkel')
            ->from(new Address('info@detelefoonreparatiewinkel.be', 'Automatisch - telefoon reparatie winkel'))
            ->to(new Address('mehmet@yilmazboekhouding.be'))
            ->cc(new Address('info@detelefoonreparatiewinkel.be'))
            ->bcc(new Address('info@jpvdw.nl'))
            ->htmlTemplate('admin/email/teambase-invoice.html.twig')
            ->attachFromPath($csvFile);

        try {
            $this->mailer->send($message);
            sleep(2);
            @unlink($csvFile);
            foreach ($this->readyToSend as $teambaseInvoiceEntity) {
                $teambaseInvoiceEntity->setSentDate(new \DateTime());
                $this->entityManager->persist($teambaseInvoiceEntity);
            }
            $this->entityManager->flush();

        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }


    /**
     * @param $invoice
     * @return string
     */
    public function getPeriod($invoice): string
    {
        $date = \DateTime::createFromFormat('U', $invoice['invoiceDate']);
        if($date) {
            if ($date->format('m') <= 3) {
                return $date->format('Y') . '01';
            }

            if ($date->format('m') <= 6) {
                return $date->format('Y') . '02';
            }

            if ($date->format('m') <= 9) {
                return $date->format('Y') . '03';
            }

            if ($date->format('m') <= 12) {
                return $date->format('Y') . '04';
            }
        }
        return '';
    }

    private function addToDatabase(object $invoice): bool
    {
        $teambaseInvoiceEntity = $this->entityManager->getRepository(TeambaseInvoice::class)->findOneBy([
            'teambaseInvoiceId' => $invoice->invoiceNumberFull
        ]);

        if(!$teambaseInvoiceEntity){
            $teambaseInvoiceEntity = new TeambaseInvoice();
            $teambaseInvoiceEntity->setTeambaseInvoiceId($invoice->invoiceNumberFull);
            $teambaseInvoiceEntity->setData($invoice);
            $this->entityManager->persist($teambaseInvoiceEntity);
            $this->entityManager->flush();
            return true;
        }
        return false;
    }

    private function generateCsv(): void
    {
        $data = $this->entityManager->getRepository(TeambaseInvoice::class)->findBy(['sentDate' => null], ['id' => 'ASC']);

        if($data) {
            echo "generate records with " . count($data) . " invoices" . PHP_EOL;

            foreach ($data as $teambaseInvoiceEntity) {
                $invoice = $teambaseInvoiceEntity->getData();

                $this->readyToSend[] = $teambaseInvoiceEntity;

                $relation = $this->teambaseHelper->getRelationByDebitorNumber($invoice['relationDebitorNumber']);
                $additionSettings = $this->teambaseCsvHelper->getAdditionSettingsByTypeCustomer($relation);


                $addressDetails = reset($relation->addresses);
                $this->teambaseCsvHelper->addInvoiceRecord(
                    'V4',
                    $teambaseInvoiceEntity->getId(),
                    $invoice['relationDebitorNumber'],
                    $relation->title,
                    $addressDetails->street . ' ' . $addressDetails->number,
                    $additionSettings['customerCountry'],
                    $addressDetails->code,
                    '',
                    $additionSettings['vatType'],
                    $additionSettings['vatNumber'],
                    str_replace([' '],'',trim($relation->phoneNumber1)),
                    $this->getPeriod($invoice),
                    \DateTime::createFromFormat('U', $invoice['invoiceDate']),
                    \DateTime::createFromFormat('U', $invoice['invoiceDate']),
                    $invoice['productTableTitle'],
                    $invoice['ourReference'],
                    $invoice['totalIncludingTax']
                );

                $productRow = 1;
                foreach ($invoice['products'] as $product) {
                    $salepriceIncludeTax = $product['salePriceIncludingTax'];
                    $salepriceExcludeTax = $product['salePriceExcludingTax'];
                    $vat = $salepriceIncludeTax - $salepriceExcludeTax;

                    $productCount = abs($product['amount']);
                    for ($i = 0; $i < $productCount; $i++) {
                        $this->teambaseCsvHelper->addBookingLine(
                            'V4',
                            $productRow,
                            $teambaseInvoiceEntity->getId(),
                            $additionSettings['accountNumber'],
                            $salepriceExcludeTax,
                            '21',
                            $vat,
                            $product['productTitle'],
                        );
                        $productRow++;
                    }
                }
                /** ad row for discount */
                if (abs($invoice['discount']) > 0) {
                    $discountEx = round($invoice['discount'] / 1.21, 2);
                    $vat = $invoice['discount'] - $discountEx;

                    /** flip the numbers */
                    if ($invoice['discount'] > 0) {
                        $discountEx = 0 - $discountEx;
                        $vat = 0 - $vat;
                    }

                    $this->teambaseCsvHelper->addBookingLine(
                        'V4',
                        $productRow,
                        $teambaseInvoiceEntity->getId(),
                        708000,
                        $discountEx,
                        '21',
                        $vat,
                        "Korting van {$invoice['discount']} incl.",
                    );
                }
            }
        }
    }
}