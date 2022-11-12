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

    public function getTreeData(): array
    {
        $sql = <<<'SQL'
            SELECT DISTINCT a.ancestor_id AS ancestor, a.prop_id AS prop, p.name AS prop_name, h.child_id AS child
            FROM ancestors a
            INNER JOIN props p ON p.id = a.prop_id
            LEFT OUTER JOIN hierarchy h ON h.parent_id = a.prop_id
            WHERE a.ancestor_id IN (
                SELECT id FROM props p2 WHERE NOT EXISTS(SELECT 1 FROM hierarchy WHERE child_id = p2.id)
            )
SQL;

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('ancestor', 'ancestorId');
        $rsm->addScalarResult('prop', 'propId');
        $rsm->addScalarResult('prop_name', 'propName');
        $rsm->addScalarResult('child', 'childId');

        return $this->getEntityManager()
            ->createNativeQuery($sql, $rsm)
            ->getResult();
    }
}
