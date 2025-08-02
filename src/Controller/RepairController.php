<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Model;
use App\Entity\ModelGroup;
use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RepairController extends AbstractController
{

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, EntityManagerInterface $em)
    {
        /** @var ModelGroup $modelGroup */
        $modelGroups = $em->getRepository(ModelGroup::class)->findAll();
        $configurator = [];

        foreach ($modelGroups as $modelGroup) {
            /** @var Model $model */
            if (!array_key_exists($modelGroup->getType(), $configurator)) {
                $configurator[$modelGroup->getType()] = [];
            }
            if (!array_key_exists($modelGroup->getBrand()->getName(), $configurator[$modelGroup->getType()])) {
                $configurator[$modelGroup->getType()][$modelGroup->getBrand()->getName()] = [];
            }
            foreach ($modelGroup->getModels() as $model) {
                array_push($configurator[$modelGroup->getType()][$modelGroup->getBrand()->getName()], [
                    'name' => $model->getName(),
                    'slug' => $model->getSlug(),
                ]);
            }
        }

        return $this->render(getenv('TEMPLATE') . '/pages/configurator/repairpage.html.twig', [
            'brands' => $em->getRepository(Brand::class)->findBy(['status' => true], ['sort' => 'ASC']),
            'session' => $request->getSession()->get('data'),
            'configurator' => $configurator,
        ]);

    }

    /**
     * @param Request $request
     * @param $brand
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function brand(Request $request, $brand, EntityManagerInterface $em)
    {

        $page = $em->getRepository(Page::class)->findOneBy([
            'slug' => "reparatie/{$brand}",
            'domain' => getenv('DOMAIN'),
            'typeTemplate' => getenv('TEMPLATE')
        ]);


        $br = $em->getRepository(Brand::class)->findOneBy([
            'slug' => $brand,
            'status' => true
        ]);

        if(!$br){
            return new Response($this->renderView(getenv('TEMPLATE') . '/pages/404.html.twig', [
                'slug' => "reparatie/{$brand}"
            ]), Response::HTTP_NOT_FOUND);
        }

        return $this->render(getenv('TEMPLATE') . '/pages/configurator/brandPage.html.twig', [
            'brand' => $br,
            'groups' => $em->getRepository(ModelGroup::class)->findBy(['brand' => $brand, 'status' => true]),
            'page' => $page
        ]);
    }

    /**
     * @param Request $request
     * @param $brand
     * @param $model
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function model(Request $request, $brand, $model, EntityManagerInterface $em)
    {

        $page = $em->getRepository(Page::class)->findOneBy([
            'slug' => "reparatie/{$brand}/{$model}",
            'domain' => getenv('DOMAIN'),
            'typeTemplate' => getenv('TEMPLATE')
        ]);

        $modelDB = $em->createQueryBuilder()
            ->select('m')
            ->from(Model::class, 'm')
            ->join('m.brand', 'b')
            ->where('b.slug = :brandslug')
            ->andWhere('m.slug = :modelslug')
            ->andWhere('m.status = 1')
            ->orderBy('m.sort', 'ASC')
            ->setParameter('brandslug', $brand)
            ->setParameter('modelslug', $model)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$modelDB) {

            return new Response($this->renderView(getenv('TEMPLATE') . '/pages/404.html.twig', [
                'slug' => "reparatie/{$brand}/{$model}"
            ]), Response::HTTP_NOT_FOUND);
        }

        return $this->render(getenv('TEMPLATE') . '/pages/configurator/modelpage.html.twig', [
            'brand' => $modelDB->getBrand(),
            'model' => $modelDB,
            'session' => $request->getSession()->get('data'),
            'page' => $page,
        ]);

    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contact(Request $request, EntityManagerInterface $em)
    {
        return $this->render(getenv('TEMPLATE') . '/pages/configurator/contactpage.html.twig', array(
            'session' => $request->getSession()->get('data')
        ));
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function computer(Request $request, EntityManagerInterface $em)
    {

        return $this->render(getenv('TEMPLATE') . '/pages/computer.html.twig', [
            'page' => $em->getRepository(Page::class)->findOneBy([
                'slug' => 'reparatie/computer',
                'domain' => getenv('DOMAIN'),
                'typeTemplate' => getenv('TEMPLATE')
            ]),
            'models' => $em->getRepository(ModelGroup::class)->findBy([
                'type' => 'Computer'
            ])
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function laptop(Request $request, EntityManagerInterface $em)
    {
        return $this->render(getenv('TEMPLATE') . '/pages/laptop.html.twig', [
            'page' => $em->getRepository(Page::class)->findOneBy([
                'slug' => 'reparatie/laptop',
                'domain' => getenv('DOMAIN'),
                'typeTemplate' => getenv('TEMPLATE')
            ]),
            'models' => $em->getRepository(ModelGroup::class)->findBy([
                'type' => 'Laptop'
            ])
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mac(Request $request, EntityManagerInterface $em){
        $groups = [];
        $models = [];
        $modelGroups = $em->getRepository(ModelGroup::class)->findBy(['id' => [9, 10, 29, 30, 31, 32, 33, 35, 36]]);
        /** @var ModelGroup $modelGroup */
        foreach ($modelGroups as $modelGroup) {
            /** @var Model $model */
            foreach ($modelGroup->getModels() as $model) {
                if ($model->getStatus() === true) {
                    array_push($models, $model);
                }
            }
            $modelGroup->setModels($models);
            array_push($groups, $modelGroup);
            $models = [];
        }
        return $this->render(getenv('TEMPLATE') . '/pages/laptop.html.twig', [
            'page' => $em->getRepository(Page::class)->findOneBy([
                'slug' => 'reparatie/mac',
                'domain' => getenv('DOMAIN'),
                'typeTemplate' => getenv('TEMPLATE')
            ]),
            'models' => $groups
        ]);
    }
}