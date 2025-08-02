<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Model;
use App\Entity\ModelGroup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;


class ConfiguratorController extends AbstractController
{
    protected $em;

    /**
     * @param EntityManagerInterface $em
     * @param $brand
     * @return Response
     */
    public function getModels(EntityManagerInterface $em, $brand)
    {
        $this->em = $em;
        switch ($brand) {
            case 'mac':
                $models = $this->getAllMacs();
                break;
            case 'laptop':
                $models = $this->getAllLaptops();
                break;
            default:
                $models = $this->getAllModelsFromBrand($em, $brand);
                break;
        }

        return $this->render('/configurator/components/brandDeviceDropdown.html.twig', [
            'models' => $models,
            'brand' => $brand
        ]);
    }

    /**
     * @param EntityManagerInterface $em
     * @param $brandSlug
     * @return array
     */
    private function getAllModelsFromBrand(EntityManagerInterface $em, $brandSlug)
    {
        $models = [];

        $brand = $em->getRepository(Brand::class)->findOneBy([
            'slug' => $brandSlug,
            'status' => true
        ]);

        /** @var ModelGroup $modelGroup */
        if($brand) {
            foreach ($brand->getGroups() as $modelGroup) {
                /** @var Model $model */
                foreach ($modelGroup->getModels() as $model) {
                    /** @var ModelGroup $modelGroup */
                    if ($modelGroup && $model->getStatus() === true) {
                        if (!array_key_exists($modelGroup->getType(), $models)) {
                            $models[$modelGroup->getType()] = [];
                        }
                        $models[$modelGroup->getType()][] = [
                            'brand' => $model->getBrand()->getSlug(),
                            'name' => $model->getName(),
                            'slug' => $model->getSlug(),
                        ];
                    }
                }
            }
        }

        return $models;
    }

    /**
     * @return array
     */
    public function getAllMacs()
    {
        $models = [];
        $modelGroups = $this->em->getRepository(ModelGroup::class)->findBy(['id' => [9, 10, 29, 30, 31, 32, 33, 35, 36]]);
        /** @var ModelGroup $modelGroup */
        foreach ($modelGroups as $modelGroup) {
            /** @var Model $model */
            foreach ($modelGroup->getModels() as $model) {
                if ($model->getStatus() === true) {
                    $models = $this->buildModelsArray($model, $models);
                }
            }
        }
        return $models;
    }

    /**
     *
     */
    private function getAllLaptops()
    {
        $models = [];
        $modelGroups = $this->em->getRepository(ModelGroup::class)->findBy(['type' => 'Laptop']);
        /** @var ModelGroup $modelGroup */
        foreach ($modelGroups as $modelGroup) {
            /** @var Model $model */
            foreach ($modelGroup->getModels() as $model) {
                if (stristr(strtolower($model->getGroup()->getName()), 'macbook') === false) {
                    if ($model->getStatus() === true) {
                        $models = $this->buildModelsArray($model, $models);
                    }
                }
            }
        }
        return $models;
    }

    /**
     * @param $model
     * @param $models
     * @return mixed
     */
    public function buildModelsArray($model, $models)
    {
        /** @var Model $model */
        if (!array_key_exists($model->getGroup()->getType(), $models)) {
            $models[$model->getGroup()->getType()] = [];
        }
        array_push($models[$model->getGroup()->getType()], [
            'brand' => $model->getBrand()->getSlug(),
            'name' => $model->getName(),
            'slug' => $model->getSlug(),
        ]);

        return $models;
    }
}