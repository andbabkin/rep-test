<?php

namespace App\Controller;

use App\Entity\Prop;
use App\Services\PropService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class PropController extends AbstractFOSRestController
{
    #[Route('/props', name: 'app_props_list', methods: ['GET', 'HEAD'])]
    public function index(PropService $propService): View
    {
        $data = $propService->getAll();

        return $this->view($data);
    }

    #[Route('/prop', name: 'app_prop_add', methods: ['POST'])]
    public function add(Request $request, PropService $propService): View
    {
        $content = json_decode($request->getContent(), true);

        $newPropName = $content['name'] ?? null;
        if (empty($newPropName)) {
            throw new UnprocessableEntityHttpException('Field "name" should not be empty');
        }

        $parent = null;
        $parentName =  $content['parent'] ?? null;
        if (!empty($parentName)) {
            $parent = $propService->getByName($parentName);
            if(is_null($parent)) {
                throw new ResourceNotFoundException('Parent not found');
            }
        }

        $prop = $propService->getByName($newPropName);
        if (is_null($prop)) {
            $prop = $propService->create($newPropName, $parent);
        } else {
            // validate
        }

        return $this->view($prop);
    }
}
