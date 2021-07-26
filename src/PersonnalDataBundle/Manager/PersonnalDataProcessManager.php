<?php

namespace Ocd\PersonnalDataBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataProcess;

class PersonnalDataProcessManager
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    public function getPersonnalDataProcessByConstantCode(string $constantCode): ?PersonnalDataProcess
    {
        $personnalDataProcess = $this->em->getRepository(PersonnalDataProcess::class)->findOneBy([
            'constantCode' => $constantCode,
        ]);
        return $personnalDataProcess;
    }
    public function makePersonnalDataProcessByConstantCode(string $constantCode): PersonnalDataProcess
    {
        $personnalDataProcess = $this->getPersonnalDataProcessByConstantCode($constantCode);
        if(!$personnalDataProcess)
        {
            $personnalDataProcess = new PersonnalDataProcess();
            $personnalDataProcess->setConstantCode($constantCode);
            $this->em->persist($personnalDataProcess);
        }
        return $personnalDataProcess;
    }
}
