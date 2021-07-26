<?php

namespace Ocd\PersonnalDataBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataConsent;

class PersonnalDataConsentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonnalDataConsent::class);
    }

}