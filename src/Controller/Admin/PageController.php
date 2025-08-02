<?php

namespace App\Controller\Admin;

use App\Entity\Brand;
use App\Entity\Model;
use App\Entity\Page;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class PageController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(PaginatorInterface $paginator, Request $request, EntityManagerInterface $entityManager)
    {
        $dql = $entityManager->createQueryBuilder()
            ->select('i')
            ->from(Page::class, 'i')
            ->orderBy('i.domain', 'DESC')
            ->addOrderBy('i.typeTemplate', 'DESC');

        if ($request->get('title')) {
            $dql->andWhere('i.metaTitle LIKE :title')
                ->setParameter('title', '%' . $request->get('title') . '%');
        }

        if ($request->get('slug')) {
            $dql->andWhere('i.slug LIKE :slug')
                ->setParameter('slug', '%' . $request->get('slug') . '%');
        }
        if ($request->get('typeTemplate')) {
            $dql->andWhere('i.typeTemplate = :typeTemplate')
                ->setParameter('typeTemplate', $request->get('typeTemplate'));
        }
        if ($request->get('domain')) {
            $dql->andWhere('i.domain = :domain')
                ->setParameter('domain', $request->get('domain'));
        }

        $dql->getQuery()->useResultCache(false);

        $pagination = $paginator->paginate(
            $dql,
            $request->query->getInt('pagina', 1),
            $request->get('aantal', 30)
        );

        return $this->render('admin/pages/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createOrUpdate(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('admin/pages/edit.html.twig', [
            'page' => $em->getRepository(Page::class)->findOneBy(['id' => $request->get('id')]),
            'brands' => $em->getRepository(Brand::class)->findAll(),
            'models' => $em->getRepository(Model::class)->findAll(),
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function savePage(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var Page $pages */
        $page = $em->getRepository(Page::class)->findOneBy(['id' => $request->get('id')]);
        if (!$page) {
            $page = new Page();
        }

        $page->setBrand(null);
        $page->setModel(null);
        switch ($request->get('template')) {
            case 'brandpage':
                $page->setBrand($em->getRepository(Brand::class)->findOneBy(['id' => $request->get('brandId')]));
                break;

            case 'modelpage':
                $page->setModel($em->getRepository(Model::class)->findOneBy(['id' => $request->get('modelId')]));
                break;
        }

        $slug = str_replace(" ", "-", trim(strtolower($request->get('slug'))));
        $page->setTemplate($request->get('template'));
        $page->setFirstContent($request->get('firstcontent'));

        $page->setMenuCategory($request->get('menuCategory'));
        if ($request->get('menuCategory') == 'NULL') {
            $page->setMenuCategory('');
        }

        $page->setMenuLinkName($request->get('menuLinkName'));
        $page->setFirstContent($request->get('firstcontent'));
        $page->setMainContent($request->get('maincontent'));
        $page->setMetaDescription($request->get('metaDescription'));
        $page->setMetaKeywords($request->get('metaKeywords'));
        $page->setMetaTitle($request->get('metaTitle'));
        $page->setDomain($request->get('domain'));
        $page->setTypeTemplate($request->get('typeTemplate'));
        $page->setStatus((bool)$request->get('status'));
        $page->setInFooter((bool)$request->get('inFooter'));
        $page->setSlug($slug);

        foreach ($request->files as $file) {
            if($file) {
                $fileName = uniqid() . $file->getClientOriginalExtension();
                $file->move(getenv('IMAGE_DIR') . 'uploads', $fileName);
                $page->setBackgroundImage('/images/uploads/' . $fileName);
            }
        }

        $em->persist($page);
        try {
            $em->flush();
            $this->addFlash('success', $page->getSlug() . ' Opgeslagen');
        } catch (\Exception $e) {
            echo $e->getMessage();
            $this->addFlash('success', $page->getSlug() . ' Opgeslagen');

        }
        return $this->redirectToRoute('admin_index_pages');

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deletepage(Request $request)
    {
        $response = array(
            'status' => 'error',
            'message' => 'Verwijderen is niet gelukt!'
        );
        /** @var EntityManager $em */
        $em = $this->container->get('doctrine')->getManager();
        if ($page = $em->getRepository(Page::class)->findOneById($request->get('pageid'))) {
            $em->remove($page);
            $em->flush();
            $response = array(
                'status' => 'success',
                'message' => 'Verwijderen is gelukt'
            );

        }
        return New JsonResponse($response);
    }


}

