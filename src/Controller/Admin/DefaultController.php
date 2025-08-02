<?php

namespace App\Controller\Admin;

use App\Entity\Brand;
use App\Entity\Model;
use App\Entity\Page;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(){
        return $this->render('admin/index.html.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function nav($route, EntityManagerInterface $entityManager){
        $brands = $entityManager->getRepository(Brand::class)->findAll();
        return $this->render('admin/default/components/menu.html.twig', [
            'brands' => $brands,
            'route' => $route
        ]);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function imageUpload(Request $request){

        $allowdTypes = ['image/png','image/jpeg','image/gif'];

        foreach ($request->files as $file) {
            if(in_array($file->getMimeType(), $allowdTypes) ) {
                $fileName = md5(uniqid() . '_' . $file->getClientOriginalName()).'.'. $file->guessExtension();
                if($file->move(getenv('WEB_DIR').'images/uploads/', $fileName)){
                    return New JsonResponse([
                        'location' => '/images/uploads/'.$fileName,
                    ]);
                }
            }
        }
        return New JsonResponse([]);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllPageUrls(EntityManagerInterface $entityManager){
        return $this->render('admin/pagelist.json.twig', [
            'landingpagesBe' => $entityManager->getRepository(Page::class)->findBy(['status' => true, 'domain' => 'be', 'brand' => NULL, 'model' => NULL]),
            'landingpagesNl' => $entityManager->getRepository(Page::class)->findBy(['status' => true, 'domain' => 'nl', 'brand' => NULL, 'model' => NULL]),
            'brands' => $entityManager->getRepository(Brand::class)->findBy(['status' => true])
        ]);
    }
}