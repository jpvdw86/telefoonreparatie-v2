<?php

namespace App\Controller\Admin;

use App\Entity\Brand;
use App\Entity\Model;
use App\Entity\ModelGroup;
use App\Entity\Repair;
use App\Entity\RepairOptions;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ModelController extends AbstractController
{

    /**
     * @param Brand $brand
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Brand $brand, EntityManagerInterface $entityManager)
    {

        $results = $entityManager->createQueryBuilder()
            ->select('i')
            ->from(Model::class, 'i')
            ->where('i.brand = :brand')
            ->setParameter('brand', $brand)
            ->orderBy('i.status', 'DESC')
            ->addOrderBy('i.sort', 'ASC')
            ->getQuery()
            ->useResultCache(false)
            ->getResult();

        return $this->render('admin/models/index.html.twig', [
            'brand' => $brand,
            'results' => $results
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function sort($brand, Request $request, EntityManagerInterface $entityManager)
    {
        if ($request->get('id')) {
            $model = $entityManager->getRepository(Model::class)->findOneBy(['id' => $request->get('id')]);
            $group = $entityManager->getRepository(ModelGroup::class)->findOneBy(['id' => $request->get('group')]);
            if ($model) {
                $model->setSort((int)$request->get('position'));
                $model->setGroup($group);
                $entityManager->persist($model);
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

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function groupSort(Request $request, EntityManagerInterface $entityManager)
    {
        if ($request->get('id')) {
            $modelGroup = $entityManager->getRepository(ModelGroup::class)->findOneBy(['id' => $request->get('id')]);
            if ($modelGroup) {
                $modelGroup->setSort((int)$request->get('position'));
                $entityManager->persist($modelGroup);
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

    /**
     * @param Brand $brand
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editOrCreateGroup(Brand $brand, Request $request, EntityManagerInterface $entityManager)
    {
        return $this->render('admin/models/edit_group.html.twig', [
            'brand' => $brand,
            'group' => $entityManager->getRepository(ModelGroup::class)->findOneBy(['id' => $request->get('id')]),
            'group_types' => $entityManager->getRepository(ModelGroup::class)
                ->createQueryBuilder('mg')
                ->select('mg.type as type')
                ->orderBy('mg.type', 'ASC')
                ->distinct('mg.type')
                ->getQuery()
                ->getArrayResult()
        ]);
    }

    /**
     * @param Brand $brand
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function saveGroup(Brand $brand, Request $request, EntityManagerInterface $entityManager)
    {
        /** @var Brand $brand */
        $group = $entityManager->getRepository(ModelGroup::class)->findOneBy(['id' => $request->get('id')]);
        if (!$group) {
            $group = new ModelGroup();
            $group->setType($request->get('type', 'Onbekend'));
            $group->setBrand($brand);
            $group->setSort(1);
        }
        $group->setName($request->get('name'));

        $entityManager->persist($group);
        $entityManager->flush();

        $this->addFlash('success', $group->getName() . ' Opgeslagen');

        return $this->redirectToRoute('admin_index_model', ['brand' => $brand->getId()]);
    }

    /**
     * @param Brand $brand
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editOrCreate(Brand $brand, Request $request, EntityManagerInterface $entityManager)
    {
        return $this->render('admin/models/edit.html.twig', [
            'brand' => $brand,
            'repairOptions' => $entityManager->getRepository(RepairOptions::class)->findAll(),
            'repairOptionsGroups' => $entityManager->getRepository(ModelGroup::class)
                ->createQueryBuilder('mg')
                ->select('mg.type as type')
                ->orderBy('mg.type', 'ASC')
                ->distinct('mg.type')
                ->getQuery()
                ->getArrayResult(),
            'model' => $entityManager->getRepository(Model::class)->findOneBy(['id' => $request->get('id')])
        ]);
    }


    /**
     * @param Brand $brand
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function saveModel(Brand $brand, Request $request, EntityManagerInterface $entityManager)
    {
        /** @var Brand $brand */
        $model = $entityManager->getRepository(Model::class)->findOneBy(['id' => $request->get('id')]);
        if (!$model) {
            $model = new Model();
            $model->setBrand($brand);
            $model->setSort(1);
        }

        $slug = str_replace(" ", "-", trim(strtolower($request->get('slug'))));
        $name = trim($request->get('name'));
        $status = (bool)$request->get('status');

        $model->setName($name);
        $model->setFirstContent($request->get('firstcontent'));
        $model->setMainContent($request->get('maincontent'));
        $model->setMetaDescription($request->get('metaDescription'));
        $model->setMetaTitle($request->get('metaTitle'));
        $model->setStatus($status);
        $model->setInFooter((bool)$request->get('inFooter'));
        $model->setIsPopular((bool)$request->get('isPopular'));
        $model->setSlug($slug);
        $entityManager->persist($model);
        $entityManager->flush();

        foreach ($request->files as $file) {
            if ($file) {
                $fileName = $model->getId() . $file->getClientOriginalExtension();
                $file->move(getenv('IMAGE_DIR') . 'models', $fileName);
            }
        }
        if (isset($fileName)) {
            $model->setImage(strtolower('/images/models/' . $fileName));
        }

        foreach ($request->get('priceNl') as $option => $price) {
            $repairOption = $entityManager->getRepository(RepairOptions::class)->findOneBy(['id' => $option]);
            $repair = $entityManager->getRepository(Repair::class)->findOneby(['repairOption' => $repairOption, 'model' => $model]);
            if (trim($price) === '' || is_null($price)) {
                if ($repair) {
                    $entityManager->remove($repair);
                }
            } else {
                if (!$repair) {
                    $repair = new Repair();
                    $repair->setRepairOption($repairOption);
                    $repair->setModel($model);
                }

                $repair->setPriceNl($price);

                if (trim($request->get('priceFromNl')[$option]) !== '' && trim($request->get('priceFromNl')[$option]) !== '0.00') {
                    $repair->setPriceFromNl($request->get('priceFromNl')[$option]);
                } else {
                    $repair->setPriceFromNl('0.00');
                }

                if (trim($request->get('priceFromBe')[$option]) !== '' && trim($request->get('priceFromBe')[$option]) !== '0.00') {
                    $repair->setPriceFromBe($request->get('priceFromBe')[$option]);
                } else {
                    $repair->setPriceFromBe('0.00');
                }

                if (trim($request->get('priceBe')[$option]) !== '') {
                    $repair->setPriceBe($request->get('priceBe')[$option]);
                } else {
                    $repair->setPriceBe($price);
                }

                if (trim($request->get('repairTimeFrom')[$option]) !== '') {
                    $repair->setRepairTimeFrom($request->get('repairTimeFrom')[$option]);
                }

                if (trim($request->get('repairTimeUntil')[$option]) !== '') {
                    $repair->setRepairTimeUntil($request->get('repairTimeUntil')[$option]);
                }

                $entityManager->persist($repair);
            }
        }

        $entityManager->persist($model);
        $entityManager->flush();

        $this->addFlash('success', $model->getName() . ' Opgeslagen');

        return $this->redirectToRoute('admin_index_model', ['brand' => $brand->getId()]);
    }
}
