<?php
/**
 * Created by PhpStorm.
 * User: Amine
 * Date: 19/08/14
 * Time: 12:37
 */

namespace Novactive\EzPublishFormGeneratorBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Novactive\EzPublishFormGeneratorBundle\Entity\Entity;

class CollectionRepository extends EntityRepository {

    public function findByEntity(Entity $entity){
        $qb = $this
            ->createQueryBuilder('collection')
            ->andWhere('collection.formEntity = :entity')
            ->setParameter('entity', $entity)
        ;

        return $qb->getQuery()->getResult();
    }

    public function countByEntity(Entity $entity){
        return $this->createQueryBuilder('collection')
            ->select('COUNT(collection)')
            ->andWhere('collection.formEntity = :entity')
            ->setParameter('entity',$entity)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function remove($collectionId){
        $qb = $this->getEntityManager()->createQueryBuilder()
                   ->delete('NovactiveEzPublishFormGeneratorBundle:Collection', 'a')
                   ->where('a.id=:value')
                   ->setParameter('value',$collectionId);
        return $qb->getQuery()->execute();
    }

} 