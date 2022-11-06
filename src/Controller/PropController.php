<?php

namespace App\Controller;

use App\Services\PropService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PropController extends AbstractFOSRestController
{
    #[Route('/props', name: 'app_props_list', methods: ['GET', 'HEAD'])]
    public function index(PropService $propService): View
    {
        $data = $propService->getTree();

        return $this->view($data);
    }

    #[Route('/prop/{id}', name: 'app_prop_get', methods: ['GET', 'HEAD'])]
    public function show(int $id, PropService $propService): View
    {
        $data = $propService->getPropData($id);
        if (is_null($data)) {
            throw $this->createNotFoundException('Property not found');
        }

        return $this->view();
    }

    #[Route('/prop', name: 'app_prop_add', methods: ['POST'])]
    public function add(Request $request, PropService $propService): View
    {
        $content = json_decode($request->getContent(), true);

        $newPropName = $content['name'] ?? null;
        if (empty($newPropName)) {
            throw new UnprocessableEntityHttpException("Field 'name' should not be empty");
        }

        $parent = null;
        $parentName =  $content['parent'] ?? null;
        if (!empty($parentName)) {
            $parent = $propService->getByName($parentName);
            if(is_null($parent)) {
                throw $this->createNotFoundException('Parent not found');
            }
        }

        $prop = $propService->getByName($newPropName);
        if (is_null($prop)) {
            $prop = $propService->create($newPropName, $parent);
        } elseif (!is_null($parent)) {
            if ($propService->isParentToSelf($prop, $parent)) {
                throw new UnprocessableEntityHttpException('Property cannot be parent to self to avoid recursion');
            }
            $propService->addToParent($prop, $parent);
        }

        $response = [
            'id' => $prop->getId(),
            'name' => $prop->getName()
        ];

        return $this->view($response);
    }
}
