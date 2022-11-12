<?php

namespace App\Services;

use App\Entity\Prop;
use App\Repository\PropRepository;
use Doctrine\Persistence\ManagerRegistry;

class PropService
{
    public function __construct(
        private readonly PropRepository $propRepository,
        private readonly ManagerRegistry $doctrine
    ) {}

    public function getByName(string $name): Prop|null
    {
        return $this->propRepository->findOneBy(['name' => $name]);
    }

    public function create(string $name, ?Prop $parent): Prop
    {
        $em = $this->doctrine->getManager();

        $prop = new Prop();
        $prop->setName($name);
        $em->persist($prop);

        if (!is_null($parent)) {
            $parent->addChild($prop);
            $em->persist($parent);
        }

        $em->flush();

        return $prop;
    }

    /**
     * @return Prop[]
     */
    public function getTree(): array
    {
        $data = [];
        $props = $this->propRepository->getRootProps();
        foreach ($props as $prop) {
            $data[] = [
                'id' => $prop->getId(),
                'name' => $prop->getName(),
                'children' => $this->getChildrenTree($prop)
            ];
        }

        return $data;
    }

    public function getChildrenTree(Prop $prop): array
    {
        $tree = [];
        $children = $prop->getChildren();
        foreach ($children as $child) {
            $tree[] = [
                'id' => $child->getId(),
                'name' => $child->getName(),
                'children' => $this->getChildrenTree($child)
            ];
        }

        return $tree;
    }

    public function isParentToSelf(Prop $prop, Prop $parent): bool
    {
        if ($prop->getId() === $parent->getId()) {
            return true;
        }

        $parents = $parent->getParents();
        foreach ($parents as $p) {
            if ($this->isParentToSelf($prop, $p)) {
                return true;
            }
        }

        return false;
    }

    public function addToParent(Prop $prop, Prop $parent): void
    {
        $em = $this->doctrine->getManager();
        $prop->addParent($parent);
        $em->persist($prop);
        $em->flush();
    }

    public function getPropData(int $id): ?array
    {
        $prop = $this->propRepository->find($id);
        if (is_null($prop)) {
            return null;
        }

        $data = $this->propRepository->getRelations($id);
        $data[] = [
            'id' => $prop->getId(),
            'property' => $prop->getName()
        ];

        usort($data, fn ($p1, $p2) => $p1['property'] <=> $p2['property']);

        return $data;
    }
}
