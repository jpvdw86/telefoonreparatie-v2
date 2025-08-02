<?php

namespace App\Controller\Admin;

use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class MessagesController extends AbstractController
{

    /**
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(PaginatorInterface $paginator, Request $request, EntityManagerInterface $entityManager){
        $dql = $entityManager->createQueryBuilder()
            ->select('i')
            ->from(Message::class, 'i')
            ->leftJoin('i.model', 'm')
            ->leftJoin('m.brand', 'b')
            ->join('i.customer', 'c')
            ->orderBy('i.sendDate', 'DESC')
            ->getQuery()
            ->disableResultCache(true);

        $pagination = $paginator->paginate(
            $dql,
            $request->query->getInt('pagina', 1),
            $request->get('aantal' , 12)
        );

        return $this->render('admin/messages/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @param Message $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detail(Message $id){
        return $this->render('admin/messages/detail.html.twig', [
            'message' => $id
        ]);
    }
}