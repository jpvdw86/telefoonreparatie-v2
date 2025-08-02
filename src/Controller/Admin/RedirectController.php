<?php

namespace App\Controller\Admin;

use App\Entity\RedirectUrl;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RedirectController extends AbstractController{

    /**
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(PaginatorInterface $paginator, Request $request, EntityManagerInterface $entityManager) {
        $dql = $entityManager->createQueryBuilder()
            ->select('i')
            ->from(RedirectUrl::class, 'i')
            ->orderBy('i.domain', 'DESC')
            ->addOrderBy('i.typeTemplate', 'DESC')
            ->getQuery();

        $pagination = $paginator->paginate(
            $dql,
            $request->query->getInt('pagina', 1),
            $request->get('aantal', 30)
        );

        return $this->render('admin/redirecturl/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createOrUpdate(Request $request, EntityManagerInterface $entityManager){
        return $this->render('admin/redirecturl/edit.html.twig', [
            'redirectUrl' => $entityManager->getRepository(RedirectUrl::class)->findOneBy([
                'id' => $request->get('id')
            ])
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function save(Request $request, EntityManagerInterface $entityManager){
        $redirectUrl = $entityManager->getRepository(RedirectUrl::class)->findOneBy([
            'id' => $request->get('id')
        ]);
        if(!$redirectUrl){
            $redirectUrl = new RedirectUrl();
        }

        $redirectUrl->setTypeTemplate($request->get('typeTemplate'));
        $redirectUrl->setDomain($request->get('domain'));
        $redirectUrl->setStatus((bool)$request->get('status'));
        $redirectUrl->setFromSlug(trim($request->get('fromSlug')));
        $redirectUrl->setToSLug(trim($request->get('toSlug')));

        $entityManager->persist($redirectUrl);
        try {
            $entityManager->flush();
            $this->addFlash('success', $redirectUrl->getFromSlug() . ' Opgeslagen');
        } catch (\Exception $e){
            $this->addFlash('error', $redirectUrl->getFromSlug() . ' niet opgeslagen, url dubbel?');
        }
        return $this->redirectToRoute('admin_index_redirect');
    }
}