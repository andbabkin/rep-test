<?php

namespace App\Repository;

use App\Entity\Prop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
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

    public function getRelations(int $propId): array
    {
        $sql = <<<'SQL'
            SELECT p.id, p.name, 'sibling' AS relation FROM hierarchy h
            INNER JOIN props p ON p.id = h.child_id
            WHERE h.parent_id IN (SELECT parent_id FROM hierarchy WHERE child_id = :id)
            AND h.child_id <> :id
            UNION
            SELECT p.id, p.name, 'parent' AS relation FROM hierarchy h
            INNER JOIN props p ON p.id = h.parent_id 
            AND h.child_id = :id
            UNION 
            SELECT p.id, p.name, 'child' AS relation FROM hierarchy h
            INNER JOIN props p ON p.id = h.child_id 
            AND h.parent_id = :id
SQL;

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('relation', 'relation');
        $rsm->addScalarResult('name', 'property');

        return $this->getEntityManager()
            ->createNativeQuery($sql, $rsm)
            ->setParameter('id', $propId)
            ->getResult();
    }
}
