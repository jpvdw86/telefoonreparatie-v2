<?php

namespace App\Controller\Admin;

use App\Entity\ModelGroup;
use App\Entity\RepairOptions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class RepairOptionsController extends AbstractController {

    /**
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(EntityManagerInterface $entityManager){
        return $this->render('admin/repairOptions/index.html.twig', [
            'repairOptions' => $entityManager->getRepository(RepairOptions::class)->findBy([],['sort'=> 'ASC'])
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createOrUpdate(Request $request, EntityManagerInterface $entityManager){
        return $this->render('admin/repairOptions/edit.html.twig', [
            'repairOption' =>$entityManager->getRepository(RepairOptions::class)->findOneBy(['id' => $request->get('id')]),
            'categories' => $entityManager->getRepository(ModelGroup::class)
                ->createQueryBuilder('mg')
                ->select('mg.type as type')
                ->orderBy('mg.type', 'ASC')
                ->distinct('mg.type')
                ->getQuery()
                ->getArrayResult()
        ]);
    }

    /**
     * @param $id
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete($id, EntityManagerInterface $entityManager){
        /** @var RepairOptions $repairOption */
        $repairOption = $entityManager->getRepository(RepairOptions::class)->findOneBy(['id' => $id]);
        foreach($repairOption->getRepairOptions() as $option){
            $entityManager->remove($option);
        }
        $entityManager->remove($repairOption);
        $entityManager->flush();
        return $this->redirectToRoute('admin_index_repair_options_index');
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function save(Request $request, EntityManagerInterface $entityManager){
        $repairOption = $entityManager->getRepository(RepairOptions::class)->findOneBy([
            'id' => $request->get('id')
        ]);
        if(!$repairOption){
            $repairOption = new RepairOptions();
        }
        $repairOption->setName($request->get('name'));
        $repairOption->setDescription($request->get('description'));
        $repairOption->setCategory($request->get('category'));
        $entityManager->persist($repairOption);
        $entityManager->flush();
        
        foreach ($request->files as $file) {
            if($file) {
                $fileName = $repairOption->getId().$file->getClientOriginalExtension();
                $file->move(getenv('IMAGE_DIR') . 'repair-images', $fileName);
            }
        }
        if (isset($fileName)) {
            $repairOption->setImage(strtolower('/images/repair-images/' . $fileName));
        }

        $entityManager->persist($repairOption);
        $entityManager->flush();

        $this->addFlash('success',$repairOption->getName().' Opgeslagen');

        return $this->redirectToRoute('admin_index_repair_options_index');

    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function sort(Request $request, EntityManagerInterface $entityManager){
        if($request->get('id')) {
            $pos = $entityManager->getRepository(RepairOptions::class)->findOneBy([
                'id' => $request->get('id')
            ]);
            if($pos) {
                $pos->setSort((int)$request->get('position'));
                $entityManager->persist($pos);
                $entityManager->flush();
                return new JsonResponse([
                    'status' => true
                ]);
            }
        }
        return new JsonResponse([
            'status' => false
        ]);
    }

}
