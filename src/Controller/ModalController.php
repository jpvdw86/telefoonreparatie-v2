<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ModalController extends AbstractController
{

    public function messenger() {
        return $this->render(getenv('TEMPLATE').'/modals/messenger.html.twig');
    }
}