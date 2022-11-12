<?php

namespace App\Services;

use App\Entity\Prop;
use App\Repository\AncestorRepository;
use App\Repository\PropRepository;
use App\Services\PropTree\TreeGenerator;
use Doctrine\Persistence\ManagerRegistry;
use Throwable;

class PropService
{
    public function __construct(
        private readonly PropRepository $propRepository,
        private readonly ManagerRegistry $doctrine,
        private readonly AncestorRepository $ancestorRepository,
        private readonly TreeGenerator $treeGenerator
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

        $em->getConnection()->beginTransaction();
        try {
            $em->flush();

            $this->ancestorRepository->createAncestors($prop, $parent);
            $em->flush();

            $em->getConnection()->commit();
        } catch (Throwable $exception) {
            $em->getConnection()->rollBack();
            throw $exception;
        }

        return $prop;
    }

    /**
     * @return Prop[]
     */
    public function getTree(): array
    {
        $data = $this->propRepository->getTreeData();

        return $this->treeGenerator->generate($data);
    }

    public function isParentToSelf(Prop $prop, Prop $parent): bool
    {
        if ($prop->getId() === $parent->getId()) {
            return true;
        }

        return $this->ancestorRepository->isAncestorToOther($prop->getId(), $parent->getId());
    }

    public function addToParent(Prop $prop, Prop $parent): void
    {
        $em = $this->doctrine->getManager();
        $prop->addParent($parent);
        $em->persist($prop);

        $em->getConnection()->beginTransaction();
        try {
            $em->flush();

            $this->ancestorRepository->updatePropTreeAncestors($prop, $parent);
            $em->flush();

            $em->getConnection()->commit();
        } catch (Throwable $exception) {
            $em->getConnection()->rollBack();
            throw $exception;
        }
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
