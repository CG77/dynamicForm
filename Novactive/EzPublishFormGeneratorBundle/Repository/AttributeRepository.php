<?php

namespace Novactive\EzPublishFormGeneratorBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Novactive\EzPublishFormGeneratorBundle\Entity\Page;

class AttributeRepository extends EntityRepository
{

    /**
     * @param Page $page
     * @return array[Attribute]
     */
    public function findByPage(Page $page)
    {
        $qb = $this
            ->createQueryBuilder('attr')
            ->andWhere('attr.page = :page')
            ->setParameter('page', $page)
            ->orderBy('attr.placement', 'ASC')
        ;

        return $qb->getQuery()->getResult();
    }
}