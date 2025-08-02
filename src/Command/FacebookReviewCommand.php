<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use App\Service\FacebookApi;
use App\Entity\Facebookreviews;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('sync:facebookreviews')]
class FacebookReviewCommand extends Command {

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly FacebookApi $facebookApi
    )
    {
        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        echo 'Start sync'.PHP_EOL;
        echo 'Nederland'.PHP_EOL;
        $this->getNederland();

        echo 'Belgie'.PHP_EOL;
        $this->getBelgie();
    }


    public function getNederland(): bool
    {
        return $this->processData(
            $this->facebookApi->getFacebookratingstoSave('384907524953595'),
            'nl'
        );
    }


    public function getBelgie(): bool
    {
        return $this->processData(
            $this->facebookApi->getFacebookratingstoSave('1434407740164140'),
            'be'
        );
    }

    public function processData($data, $domain): true
    {
        foreach ($data As $review) {

            echo $review['FacebookUserId'].' > '.$review['FacebookName'].' > ';
            $new = $this->em->getRepository(Facebookreviews::class)->findOneBy([
                'facebookId' => $review['FacebookUserId']
            ]);
            if (!$new) {
                echo 'Added'.PHP_EOL;
                $new = new Facebookreviews;
                $new->setDate($review['Date']);
                $new->setFacebookId($review['FacebookUserId']);
                $new->setFacebookName($review['FacebookName']);
                $new->setRating($review['Rating']);
                $new->setReviewtext(str_replace('�','', $review['Review']));
                $new->setFacebookUserImage($review['FacebookUserImage']);
                $new->setDomain($domain);
                $this->em->persist($new);
            }
            else {
                echo '(image) Updated'.PHP_EOL;
                $new->setFacebookUserImage($review['FacebookUserImage']);
                $new->setReviewtext(str_replace('�','', $review['Review']));
                $this->em->persist($new);
            }
        }
        $this->em->flush();
        return true;
    }
}