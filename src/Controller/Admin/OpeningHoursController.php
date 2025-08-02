<?php

namespace App\Controller\Admin;

use App\Entity\OpeningHours;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class OpeningHoursController extends AbstractController
{

    /**
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function index(EntityManagerInterface $entityManager)  {
        return $this->render('admin/openinghours/index.html.twig', [
            'data' => $entityManager->getRepository(OpeningHours::class)->findBy([],[
                'domain' => 'DESC',
                'typeTemplate' => 'DESC',
                'day' => 'ASC',
            ]),
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $entityManager) {
        return $this->render('admin/openinghours/edit.html.twig', [
            'days' => OpeningHours::DAYNAMES,
            'data' => $entityManager->getRepository(OpeningHours::class)->findOneBy(['id' => $request->get('id')]),
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function save(Request $request, EntityManagerInterface $entityManager) {
        $openingsHour = $entityManager->getRepository(OpeningHours::class)->findOneBy(['id' => $request->get('id')]);
        if(!$openingsHour){
            $openingsHour = new OpeningHours();
        }
        $openingsHour->setDomain($request->get('domain'));
        $openingsHour->setTypeTemplate($request->get('typeTemplate'));
        $openingsHour->setDay($request->get('day'));
        $openingsHour->setOpeningHour(new \DateTime($request->get('openingHour')));
        $openingsHour->setClosingHour(new \DateTime($request->get('closingHour')));
        $openingsHour->setComment($request->get('comment'));

        $entityManager->persist($openingsHour);
        $entityManager->flush();

        return $this->redirectToRoute('admin_index_openinghours');
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Request $request, EntityManagerInterface $entityManager): Response
    {
        $openingsHour = $entityManager->getRepository(OpeningHours::class)->findOneBy(['id' => $request->get('id')]);
        if ($openingsHour) {
            $entityManager->remove($openingsHour);
            $entityManager->flush();
        }
        return $this->redirectToRoute('admin_index_openinghours');
    }
}
