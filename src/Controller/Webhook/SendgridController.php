<?php

namespace App\Controller\Webhook;

use App\Entity\Message;
use App\Entity\MessageTracking;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SendgridController extends AbstractController
{

    public function sendgrid(Request $request, EntityManagerInterface $entityManager)
    {
        $body = json_decode($request->getContent(), true);

        foreach($body as $data){

            $messageId = str_replace(['<','>'],'', $data['smtp-id']);

            /** set Basic stuff */
            $dateTime = new \DateTime();

            $event = $data['event'];
            $message = $entityManager->getRepository(Message::class)->findOneBy(['messageId' => $messageId]);
            if($message) {
                /** @var MessageTracking $messageTracking */
                $messageTracking = new MessageTracking();
                $messageTracking->setMessage($message);
                $messageTracking->setDatetime($dateTime->setTimestamp($data['timestamp']));
                $messageTracking->setType($event);
                $messageTracking->setEmailadress($data['email']);

                if(isset($data['reason'])) {
                    $messageTracking->setDescription($data['reason']);
                }

                if($event == 'click') {
                    $messageTracking->setUrl($data['url']);
                    $messageTracking->setIpadress($data['ip']);
                }
                $entityManager->persist($messageTracking);
                $entityManager->flush();
            }
        }

        return new Response('Message received',200);
    }
}