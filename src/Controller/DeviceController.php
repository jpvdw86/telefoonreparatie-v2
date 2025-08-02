<?php

namespace App\Controller;

use App\Entity\Model;
use App\Entity\Repair;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class DeviceController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param $index
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function edit($index, Request $request, EntityManagerInterface $entityManager)
    {
        $data = $request->getSession()->get('data');
        if (isset($data['devices'][$index])) {
            $device = $data['devices'][$index];
            $model = $entityManager->getRepository(Model::class)->find($device['model_id']);

            return $this->redirectToRoute('reparatie_model', [
                'brand' => $model->getBrand()->getSlug(),
                'model' => $model->getSlug(),
                'deviceIndex' => $index
            ]);
        }
        return $this->redirectToRoute('reparatie_index');
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function save(Request $request)
    {
        $this->saveSessionData($request);

        return $this->redirectToRoute('pages', ['slug' => 'reparatie/']);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function saveAndComplete(Request $request)
    {
        $this->saveSessionData($request);
        return $this->redirectToRoute('reparatie_contact');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request)
    {
        $data = $request->getSession()->get('data');
        unset($data['devices'][$request->get('index')]);
        $request->getSession()->set('data', $data);
        return $this->redirectToRoute('pages', ['slug' => 'reparatie/']);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function saveSessionData(Request $request)
    {

        $session = $request->getSession();
        $data = $session->get('data');

        $deviceIndex = $request->get('deviceIndex', uniqid());

        if ($deviceIndex && !isset($data['devices'][$deviceIndex])) {
            $data['devices'][$deviceIndex] = [
                'color' => $request->get('color'),
                'brand' => $request->get('brand'),
                'brand_id' => $request->get('brand_id'),
                'model' => $request->get('model'),
                'model_id' => $request->get('model_id'),
                'repairs' => []
            ];
        }
        foreach ($request->get('repair') as $repair) {
            if (!isset($data['devices'][$deviceIndex]['repairs'][$repair])) {
                $data['devices'][$deviceIndex]['repairs'][$repair] = [
                    'id' => $repair,
                    'name' => $this->em->getRepository(Repair::class)->find($repair)->getRepairOption()->getName()
                ];
            }
        }

        $session->set('data', $data);

        return $session->get('data');
    }

}