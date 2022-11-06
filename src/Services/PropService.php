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
    public function getAll(): array
    {
        return $this->propRepository->findAll();
    }
}
