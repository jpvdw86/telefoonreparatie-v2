<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Page;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SitemapController extends AbstractController
{

    /**
     * @return Response
     */
    public function index(){

        $response = new Response($this->renderView('sitemaps/index.xml.twig'));
        $response->headers->set('Content-Type', 'application/xml; charset=utf-8');
        return $response;
    }

    /**
     * @return Response
     */
    public function brandModel(){
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $response = new Response($this->renderView('sitemaps/brands-models.xml.twig', [
            'brands' => $em->getRepository(Brand::class)->findBy(['status' => true], ['sort' => 'ASC'])
        ]));
        $response->headers->set('Content-Type', 'application/xml; charset=utf-8');
        return $response;

    }

    public function landingPages(){
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $response = new Response($this->renderView('sitemaps/landingPages.xml.twig', [
            'pagesUrls' => $em->getRepository(Page::class)->findBy([
                'domain' => getenv('DOMAIN'),
                'typeTemplate' => getenv('TEMPLATE'),
                'status' => true
            ])
        ]));
        $response->headers->set('Content-Type', 'application/xml; charset=utf-8');
        return $response;
    }
}