<?php

namespace App\Controller\Admin;

use App\Entity\Brand;
use App\Entity\Model;
use App\Entity\ModelGroup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class BrandsController extends AbstractController
{

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        $brands = $entityManager->createQueryBuilder()
            ->select('i')
            ->from(Brand::class, 'i')
            ->orderBy('i.sort', 'ASC')
            ->getQuery()
            ->disableResultCache()
            ->getResult();


        return $this->render('admin/brands/index.html.twig', [
            'brands' => $brands
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function sort(Request $request, EntityManagerInterface $entityManager)
    {
        if ($request->get('id')) {
            $brand = $entityManager->getRepository(Brand::class)->findOneBy(['id' => $request->get('id')]);
            if ($brand) {
                $brand->setSort((int)$request->get('position'));
                $entityManager->persist($brand);
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editOrCreate(Request $request, EntityManagerInterface $entityManager)
    {
        return $this->render('admin/brands/edit.html.twig', [
            'brand' => $entityManager->getRepository(Brand::class)->findOneBy(['id' => $request->get('id')])
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function saveBrand(Request $request, EntityManagerInterface $entityManager)
    {

        $brand = $entityManager->getRepository(Brand::class)->findOneBy(['id' => $request->get('id')]);
        if (!$brand) {
            $brand = new Brand();
        }

        $slug = str_replace(" ", "-", trim(strtolower($request->get('slug'))));
        $name = ucfirst(trim($request->get('name')));
        $status = (bool)$request->get('status');

        $brand->setName($name);
        $brand->setFirstContent($request->get('firstcontent'));
        $brand->setMainContent($request->get('maincontent'));
        $brand->setMetaDescription($request->get('metaDescription'));
        $brand->setMetaTitle($request->get('metaTitle'));
        $brand->setInFooter((bool)$request->get('inFooter'));
        $brand->setStatus($status);
        $brand->setSlug($slug);

        $bg_image = $request->files->get('background-image');
        $logo_image = $request->files->get('logo');

        if (isset($bg_image)) {
            $fileName = strtolower(str_replace([" ", "/", "\\", "|"], "_", $brand->getName()) . '-background.' . $bg_image->getClientOriginalExtension());
            $bg_image->move(getenv('IMAGE_DIR') . 'brands/', $fileName);
            $brand->setBackground(strtolower('/images/brands/' . $fileName));
        }

        if (isset($logo_image)) {
            $fileName = strtolower(str_replace([" ", "/", "\\", "|"], "_", $brand->getName()) . '.' . $logo_image->getClientOriginalExtension());
            $logo_image->move(getenv('IMAGE_DIR') . 'brands', $fileName);
            $brand->setImage(strtolower('/images/brands/' . $fileName));
        }

        $entityManager->persist($brand);
        $entityManager->flush();

        $this->addFlash('success', $brand->getName() . ' Opgeslagen');

        return $this->redirectToRoute('admin_index_brands');
    }
}