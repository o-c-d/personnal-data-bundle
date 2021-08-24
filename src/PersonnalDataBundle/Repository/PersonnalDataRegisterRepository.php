<?php

namespace Ocd\PersonnalDataBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataProvider;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataRegister;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataTransport;

class PersonnalDataRegisterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonnalDataRegister::class);
    }

    public function findByPersonnalDataProvider(PersonnalDataProvider $personnalDataProvider)
    {
        $qb = $this->createQueryBuilder('pdr');
        $qb->select('pdr');
        $qb->leftJoin(PersonnalDataConsent::class, 'pdc',  'WITH',  'pdc.personnal_data_provider_id = :personnal_data_provider_id');
        $qb->leftJoin(PersonnalDataTransport::class, 'pdt',  'WITH',  'pdt.personnal_data_provider_id = :personnal_data_provider_id');
        $qb->setParameter('personnal_data_provider_id', $personnalDataProvider->getId());
        return $qb
            ->getQuery()
            ->getResult() ;
    }

    public function findByEntity($entity)
    {
        $entityName = ClassUtils::getClass($entity);
        $entityId = $entity->getId();
        $qb = $this->createQueryBuilder('pdr');
        $qb->select('pdr.entityName');
        $qb->addSelect('pdr.fieldName');
        $qb->groupBy('pdr.entityName', 'pdr.fieldName');
        if(null!==$entityName)
        {
            $qb->andWhere('pdr.entityName = :entityName');
            $qb->setParameter('entityName', $entityName);
        }
        if(null!==$entityId)
        {
            $qb->andWhere('pdr.entityId = :entityId');
            $qb->setParameter('entityId', $entityId);
        }
        return $qb
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY) ;
    }
}