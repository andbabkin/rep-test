<?php

namespace App\Repository;

use App\Entity\Prop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Prop>
 *
 * @method Prop|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prop|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prop[]    findAll()
 * @method Prop[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prop::class);
    }

    public function save(Prop $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Prop $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getRootProps(): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.parents', 'r')
            ->where('r.id IS NULL')
            ->getQuery()
            ->getResult();
    }
}
