<?php

namespace Ocd\PersonnalDataBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataProcessType;

class PersonnalDataProcessTypeManager
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    public function getPersonnalDataProcessType(string $type): ?PersonnalDataProcessType
    {
        $personnalDataProcessType = $this->em->getRepository(PersonnalDataProcessType::class)->findOneBy([
            'constantCode' => $type,
        ]);
        return $personnalDataProcessType;
    }
    public function makePersonnalDataProcessType(string $type, string $description=''): PersonnalDataProcessType
    {
        $personnalDataProcessType = $this->getPersonnalDataProcessType($type);
        if(!$personnalDataProcessType)
        {
            $personnalDataProcessType = new PersonnalDataProcessType();
            $personnalDataProcessType->setConstantCode($type);
            $personnalDataProcessType->setDescription($description);
            $this->em->persist($personnalDataProcessType);
        }
        return $personnalDataProcessType;
    }

}