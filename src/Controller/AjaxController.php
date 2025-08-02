<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Customer;
use App\Entity\Message;
use App\Entity\MessageDevice;
use App\Entity\Model;
use App\Entity\Repair;
use App\Service\PostcodeApi;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;


class AjaxController extends AbstractController
{
    public function postcode(Request $request, PostcodeApi $postcodeApi): JsonResponse
    {
        if (!$this->isCsrfTokenValid('api', $request->get('token'))) {
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }
        return $postcodeApi->postcodeophalen($request->get('zipcode'), $request->get('houseNumber'));
    }

    /**
     * @param Brand $brand
     * @return JsonResponse
     */
    public function getModals(Brand $brand): JsonResponse
    {
        $modelOutput = '';
        foreach ($brand->getModels() as $model) {
            if ($model->getStatus()) {
                $modelOutput .= '<option value="' . $model->getId() . '">' . $model->getName() . '</option>';
            }
        }
        return new JsonResponse([
            'status' => 'success',
            'models' => $modelOutput
        ]);
    }



    public function sendcontactform(Request $request, MailerInterface $mailer): JsonResponse
    {
        if ($this->isCsrfTokenValid('contactformulier', $request->get('_csrf_token'))) {

            $email = trim(strtolower($request->get('email')));
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();

            if (trim($request->get('firstName')) != '' AND trim($request->get('lastName')) != '') {
                $aanhef = 'Beste ' . $request->get('firstName') . ' ' . $request->get('lastName');

                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    if (!$customer = $em->getRepository(Customer::class)->findOneBy(['email' => $email])) {
                        $customer = new Customer();
                        $customer->setCity($request->get('city'));
                        $customer->setEmail($email);
                        $customer->setHouseNumber($request->get('houseNumber'));
                        $customer->setZipcode(str_replace(' ', '', strtoupper($request->get('zipcode'))));
                        $customer->setStreetName($request->get('streetName'));
                        $customer->setProvince($request->get('province'));
                        $customer->setLastName($request->get('lastName'));
                        $customer->setFirstName($request->get('firstName'));
                        $customer->setPhoneNumber($request->get('phoneNumber'));
                        $customer->setCreateDate(new \DateTime());
                    } else {
                        if (trim($request->get('city')) != '') {
                            $customer->setCity($request->get('city'));
                        }
                        if (!is_null($request->get('houseNumber'))) {
                            $customer->setHouseNumber($request->get('houseNumber'));
                        }
                        if (trim($request->get('zipcode')) != '') {
                            $customer->setZipcode(str_replace(' ', '', strtoupper($request->get('zipcode'))));
                        }
                        if (trim($request->get('streetName')) != '') {
                            $customer->setStreetName($request->get('streetName'));
                        }
                        if (trim($request->get('province')) != '') {
                            $customer->setProvince($request->get('province'));
                        }
                        if (trim($request->get('lastName')) != '') {
                            $customer->setLastName($request->get('lastName'));
                        }
                        if (trim($request->get('firstName')) != '') {
                            $customer->SetFirstName($request->get('firstName'));
                        }
                        if (trim($request->get('phoneNumber')) != '') {
                            $customer->setPhoneNumber($request->get('phoneNumber'));
                        }
                    }
                    $em->persist($customer);
                    $em->flush();

                    $message = (new TemplatedEmail())
                        ->subject('Reparatie Aanvraag')
                        ->from(new Address(getenv('EMAIL'),getenv('EMAIL_NAME')))
                        ->to(new Address($email, $request->get('firstName') . ' ' . $request->get('lastName')))
                        ->replyTo(new Address(getenv('EMAIL'),getenv('EMAIL_NAME')))
                        ->htmlTemplate(getenv('TEMPLATE') . '/email/contact.html.twig')
                        ->context([
                            'aanhef' => $aanhef,
                            'customer' => $customer
                        ]);

                    $emailMessage = new Message();
                    $emailMessage->setDomain(getenv('DOMAIN'));
                    $emailMessage->setTypeTemplate(getenv('TEMPLATE'));
                    $emailMessage->setMessageId($message->generateMessageId());
                    $emailMessage->setEmailBody($message->getHtmlBody());
                    $emailMessage->setMessage($request->get('comment'));
                    $emailMessage->setSendDate(new \DateTime());
                    $emailMessage->setCustomer($customer);
                    $emailMessage->setIncomming(true);
                    $emailMessage->setAppointmentType($request->get('appointment_type'));

                    $sessionData = $request->getSession()->get('data');
                    if(isset($sessionData['devices'])){
                        foreach ($sessionData['devices'] as $uniqueId => $device) {
                            $model = $em->getRepository(Model::class)->findOneBy(['id' => $device['model_id']]);
                            if($model) {
                                $messageDevice = new MessageDevice();
                                if($device['color']) {
                                    $messageDevice->setColor($device['color']);
                                }
                                $messageDevice->setModel($model);
                                $messageDevice->setMessage($emailMessage);

                                foreach ($device['repairs'] as $repairId => $repair) {
                                    if ($repair = $em->getRepository(Repair::class)->findOneBy(['id' => $repairId])) {
                                        $messageDevice->addRepair($repair);
                                    }
                                }
                                $em->persist($messageDevice);
                            }
                        }
                    }

                    $em->persist($emailMessage);
                    $em->flush();

                    $messageDevices = $em->getRepository(MessageDevice::class)->findBy(['message' => $emailMessage->getId()]);
                    $messageIntern = (new TemplatedEmail())
                        ->subject('Reparatie Aanvraag - intern')
                        ->to(new Address(getenv('EMAIL'),getenv('EMAIL_NAME')))
                        ->from(new Address(getenv('EMAIL'),getenv('EMAIL_NAME')))
                        ->replyTo(new Address($email, $request->get('firstName') . ' ' . $request->get('lastName')))
                        ->htmlTemplate(getenv('TEMPLATE') . '/email/contactIntern.html.twig')
                        ->context([
                            'aanhef' => $aanhef,
                            'customer' => $customer,
                            'message' => $emailMessage,
                            'devices' => $messageDevices
                        ]);


                    $mailer->send($message);
                    $mailer->send($messageIntern);
                    /** clear session data */
                    $request->getSession()->set('data', []);

                    $response = [
                        'status' => 'success',
                        'message' => 'Bericht is verzonden',
                        'mailer' => 1
                    ];
                } else {
                    $response = [
                        'status' => 'error',
                        'message' => 'Geen geldig e-mailadres'
                    ];
                }
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Voor en achternaam is vereist (benodigd voor de factuur)'
                ];
            }
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Beveiligingstoken onjuist'
            ];
        }
        return new JsonResponse($response);
    }


}