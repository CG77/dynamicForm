<?php
/**
 * Created by PhpStorm.
 * User: Amine
 * Date: 26/08/14
 * Time: 10:58
 */

namespace Novactive\EzPublishFormGeneratorBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Novactive\EzPublishFormGeneratorBundle\Entity\Entity;

class PageRepository extends EntityRepository {

    public function countByEntity(Entity $entity){
        return $this->createQueryBuilder('page')
            ->select('COUNT(page)')
            ->andWhere('page.entity = :entity')
            ->setParameter('entity',$entity)
            ->getQuery()
            ->getSingleScalarResult();
    }

} 