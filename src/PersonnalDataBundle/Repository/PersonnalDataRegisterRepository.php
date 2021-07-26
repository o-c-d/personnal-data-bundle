<?php

namespace Ocd\PersonnalDataBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

}