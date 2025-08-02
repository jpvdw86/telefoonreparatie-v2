<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Facebookreviews;
use App\Entity\Model;
use App\Entity\ModelGroup;
use App\Entity\Page;
use App\Entity\OpeningHours;
use App\Entity\RedirectUrl;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{

    /**
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function index(EntityManagerInterface $entityManager)
    {
        $facebookRate = $entityManager->getRepository(Facebookreviews::class)->getAverage(getenv('DOMAIN'));
        $facebookData = $entityManager->getRepository(Facebookreviews::class)->getReviews(getenv('DOMAIN'), 5);
        $page = $entityManager->getRepository(Page::class)->findOneBy(['slug' => '/', 'domain' => getenv('DOMAIN'), 'typeTemplate' => getenv('TEMPLATE')]);
        $reviews = [];

        /** @var Facebookreviews $val */
        foreach ($facebookData as $val) {
            $reviews[] = [
                'date' => $val->getDate(),
                'customername' => $val->getFacebookName(),
                'customerimage' => $val->getFacebookUserImage(),
                'rating' => ($val->getRating() / 10),
                'review' => $val->getReviewtext()
            ];
        }
        return $this->render(getenv('TEMPLATE') . '/pages/homepage.html.twig', [
            'facebookreviews' => $reviews,
            'avg_rating' => ($facebookRate['avg_rating'] / 10),
            'total_amount' => $facebookRate['total_amount'],
            'page' => $page,
        ]);
    }

    /**
     *
     */
    public function renderConfigurator(EntityManagerInterface $entityManager)
    {
        $brands = $entityManager->getRepository(Brand::class)->findBy([], ['sort' => 'ASC']);
        $configurator = [];


        /** @var Brand $brand */
        foreach ($brands as $brand) {
            /** @var ModelGroup $modelGroup */
            foreach ($brand->getGroups() as $modelGroup) {
                /** @var Model $model */
                foreach ($modelGroup->getModels() as $model) {
                    /** @var ModelGroup $modelGroup */
                    if ($modelGroup) {
                        if ($model->getStatus() === true) {
                            if (!array_key_exists($modelGroup->getType(), $configurator)) {
                                $configurator[$modelGroup->getType()] = [];
                            }
                            if (!array_key_exists($brand->getName(), $configurator[$modelGroup->getType()])) {
                                $configurator[$modelGroup->getType()][$brand->getName()] = [];
                            }

                            array_push($configurator[$modelGroup->getType()][$brand->getName()], [
                                'name' => $model->getName(),
                                'slug' => $model->getSlug(),
                                'position' => $model->getSort()
                            ]);
                        }
                    }
                }
            }
        }
        return $this->render('/configurator/components/horizontalConfigurator.html.twig', [
            'configurator' => $configurator
        ]);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function renderPopularModels(EntityManagerInterface $entityManager)
    {
        $models = $entityManager->getRepository(Model::class)->findBy(['isPopular' => true]);
        return $this->render('/bus/default/components/popularModels.html.twig', [
            'models' => $models
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function contact(Request $request, EntityManagerInterface $entityManager)
    {
        return $this->render(getenv('TEMPLATE') . '/pages/contactpage.html.twig', [
            'page' => $entityManager->getRepository(Page::class)->findOneBy(['slug' => 'contact', 'typeTemplate' => getenv('TEMPLATE'), 'domain' => getenv('DOMAIN')]),
            'brandList' => $entityManager->getRepository(Brand::class)->findBy(['status' => true], ['sort' => 'ASC']),
            'OpeningList' => $entityManager->getRepository(OpeningHours::class)->findBy(['domain' => getenv('DOMAIN')])
        ]);
    }

    /**
     * @param Request $request
     * @param $slug
     * @param EntityManagerInterface $entityManager
     * @return bool|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function pages(Request $request, $slug, EntityManagerInterface $entityManager)
    {
        $page = $entityManager->getRepository(Page::class)->findOneBy([
            'slug' => $slug,
            'domain' => getenv('DOMAIN'),
            'typeTemplate' => getenv('TEMPLATE'),
            'status' => true
        ]);

        if ($page) {
            return $this->checkPage($page, $request);
        }

        $foundBrandOrModel = $this->checkSlugOnBrandAndOrModel($slug, $entityManager);
        if ($foundBrandOrModel) {
            return $foundBrandOrModel;
        }

        $checkRedirect = $this->checkRedirect($slug, $entityManager);
        if ($checkRedirect) {
            return $checkRedirect;
        }

        return new Response($this->renderView(getenv('TEMPLATE') . '/pages/404.html.twig', ['slug' => $slug]), 404);
    }

    /**
     * @param $slug
     * @param EntityManagerInterface $entityManager
     * @return bool|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function checkRedirect($slug, EntityManagerInterface $entityManager)
    {
        $redirect = $entityManager->getRepository(RedirectUrl::class)->findOneBy([
            'fromSlug' => $slug,
            'domain' => getenv('DOMAIN'),
            'typeTemplate' => getenv('TEMPLATE'),
            'status' => true
        ]);
        if ($redirect) {
            return $this->redirectToRoute('pages', ['slug' => $redirect->getToSlug()], 301);
        }
        return false;
    }

    /**
     * @param $slug
     * @return bool|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function checkSlugOnBrandAndOrModel($slug, EntityManagerInterface $entityManager)
    {
        $brand = null;
        $model = null;
        $slugArray = explode('/', strtolower($slug));
        foreach ($slugArray as $slugPart) {
            if (!$brand) {
                $brand = $entityManager->getRepository(Brand::class)->findOneBy([
                    'slug' => trim(str_replace(['-reparatie'], '', $slugPart))
                ]);
            }
            if (!$model) {
                if ($brand) {
                    $model = $entityManager->getRepository(Model::class)->findOneBy([
                        'slug' => str_replace('-reparatie', '', $slugPart),
                        'brand' => $brand
                    ]);
                } else {
                    $model = $entityManager->getRepository(Model::class)->findOneBy([
                        'slug' => trim(str_replace(['-reparatie'], '', $slugPart))
                    ]);
                }
            }
        }
        if ($model) {
            return $this->redirectToRoute('reparatie_model', [
                'brand' => $model->getBrand()->getSlug(),
                'model' => $model->getSlug()
            ], 301);
        } elseif ($brand) {
            return $this->redirectToRoute('reparatie_brand', [
                'brand' => $brand->getSlug()
            ],
                301
            );
        }
        return false;
    }

    /**
     * @param Page $page
     * @param $request
     * @return Response
     */
    protected function checkPage(Page $page, $request)
    {
        switch ($page->getTemplate()) {
            case 'brandpage':
                return $this->render(getenv('TEMPLATE') . '/pages/configurator/brandPage.html.twig', [
                    'page' => $page,
                    'brand' => $page->getBrand(),
                    'session' => $this->sessionData($request, [
                        'brandId' => $page->getBrand()->getId()
                    ])
                ]);
            case 'modelpage':
                return $this->render(getenv('TEMPLATE') . '/pages/configurator/modelpage.html.twig', [
                    'page' => $page,
                    'brand' => $page->getModel()->getBrand(),
                    'model' => $page->getModel(),
                    'session' => $this->sessionData($request, [
                        'model' => $page->getModel()->getId()
                    ])
                ]);

            case 'repairpage':
                return $this->render(getenv('TEMPLATE') . '/pages/configurator/repairpage.html.twig', [
                    'page' => $page,
                    'brands' => $this->getDoctrine()->getRepository(Brand::class)->findBy(['status' => true], ['sort' => 'ASC']),
                    'session' => $this->sessionData($request)
                ]);

            default:
                return $this->render(getenv('TEMPLATE') . '/pages/' . $page->getTemplate() . '.html.twig', [
                    'page' => $page
                ]);
        }
    }


    /**
     * Navigation
     * @return Response
     */
    public function navigation($route, EntityManagerInterface $entityManager)
    {

        $menuItems = $entityManager->createQueryBuilder()
            ->select('i')
            ->from(Page::class, 'i')
            ->where('i.status = :true')
            ->setParameter('true', true)
            ->andWhere('char_length(i.menuCategory) > 3')
            ->andWhere('i.domain = :domain')
            ->andWhere('i.typeTemplate = :template')
            ->setParameter('domain', getenv('DOMAIN'))
            ->setParameter('template', getenv('TEMPLATE'))
            ->orderBy('i.menuCategory', 'DESC')
            ->getQuery()
            ->getResult();

        $mI = [
            'Locaties' => [],
            'Informatie' => [],
            'Overige' => []
        ];

        foreach ($menuItems as $menuItem) {
            $menuCategory = $menuItem->getMenuCategory() == '' ? 'Overige' : $menuItem->getMenuCategory();
            array_push($mI[$menuCategory], [
                'name' => $menuItem->getMenuLinkName(),
                'slug' => $menuItem->getSlug()
            ]);
        }

        $facebookRate = $entityManager->getRepository(Facebookreviews::class)->getAverage(getenv('DOMAIN'));
        return $this->render(getenv('TEMPLATE') . '/default/components/navigation.html.twig', [
            'route' => $route,
            'menuItems' => $menuItems,
            'menuItemsNew' => $mI,
            'avg_rating' => ($facebookRate['avg_rating'] / 10),
            'total_amount' => $facebookRate['total_amount'],
            'brands' => $entityManager->getRepository(Brand::class)->findBy(['status' => true], ['sort' => 'ASC']),
        ]);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function footer(EntityManagerInterface $entityManager)
    {
        $pages = $entityManager->getRepository(Page::class)
            ->findBy(['inFooter' => true, 'typeTemplate' => getenv('TEMPLATE'), 'domain' => getenv('DOMAIN')]);
        $brands = $entityManager->getRepository(Brand::class)
            ->findBy(['inFooter' => true]);
        $models = $entityManager->getRepository(Model::class)
            ->findBy(['inFooter' => true]);

        return $this->render(getenv('TEMPLATE') . '/default/components/footer.html.twig', [
            'pages' => $pages,
            'brands' => $brands,
            'models' => $models
        ]);
    }

    /**
     * @param Request $request
     * @param array $data
     * @return mixed
     */
    public function sessionData(Request $request, $data = [])
    {

        $session = $request->getSession();
        if (empty($session->get('data'))) {
            $newSession = [];
            $session->set('data', $newSession);
        }

        if (!empty($data)) {
            $newSession = $session->get('data');
            foreach ($data as $key => $value) {
                $newSession[$key] = $value;
            }
            $session->set('data', $newSession);
        }

        return $session->get('data');
    }
}
