<?php

namespace App\Repository;

use App\Entity\Ancestor;
use App\Entity\Prop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ancestor>
 *
 * @method Ancestor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ancestor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ancestor[]    findAll()
 * @method Ancestor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AncestorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ancestor::class);
    }

    public function save(Ancestor $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Ancestor $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function createAncestors(Prop $prop, ?Prop $parent): void
    {
        if (!is_null($parent)) {
            $parentAncestors = $this->findBy(['prop' => $parent->getId()]);
            foreach ($parentAncestors as $ancestor) {
                $new = clone $ancestor;
                $new->setProp($prop);
                $this->getEntityManager()->persist($new);
            }
        }

        $new = new Ancestor();
        $new->setProp($prop);
        $new->setAncestor($prop);
        $this->getEntityManager()->persist($new);

        $this->getEntityManager()->flush();
    }

    public function updatePropTreeAncestors(Prop $prop, Prop $parent)
    {
        $propTree = $this->findBy(['ancestor' => $prop->getId()]);
        foreach ($propTree as $node) {
            $this->updateAncestors($node, $parent);
        }
        $this->getEntityManager()->flush();
    }

    private function updateAncestors(Ancestor $node, Prop $parent)
    {
        $dql = <<<'DQL'
            SELECT a FROM App\Entity\Ancestor a
            WHERE a.prop = :parent
            AND NOT EXISTS(
                SELECT 1 FROM App\Entity\Ancestor a2 
                WHERE a2.prop = :prop AND a.ancestor = a2.ancestor
            )
DQL;

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('prop', $node->getProp());
        $query->setParameter('parent', $parent);

        $ancestors = $query->getResult();
        foreach ($ancestors as $ancestor) {
            /** @var Ancestor $ancestor */
            $new = clone $ancestor;
            $new->setProp($node->getProp());
            $this->getEntityManager()->persist($new);
        }
    }

    public function isAncestorToOther(int $propId, int $otherId): bool
    {
        $sql = <<<'SQL'
            SELECT COUNT(*) AS c FROM ancestors
            WHERE prop_id = :other
            AND ancestor_id IN (
                SELECT prop_id FROM ancestors WHERE ancestor_id = :prop
            )
SQL;

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('c', 'count');

        $r = $this->getEntityManager()
            ->createNativeQuery($sql, $rsm)
            ->setParameter('prop', $propId)
            ->setParameter('other', $otherId)
            ->getResult();

        return $r[0]['count'] > 0;
    }
}
