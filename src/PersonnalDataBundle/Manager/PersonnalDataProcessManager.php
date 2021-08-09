<?php

namespace Ocd\PersonnalDataBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Ocd\PersonnalDataBundle\Manager\PersonnalDataProcessTypeManager;
use Ocd\PersonnalDataBundle\Manager\PersonnalDataRegisterManager;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataProcess;

class PersonnalDataProcessManager
{
    private EntityManagerInterface $em;
    private PersonnalDataProcessTypeManager $personnalDataProcessTypeManager;
    private PersonnalDataRegisterManager $personnalDataRegisterManager;

    public function __construct(EntityManagerInterface $em, PersonnalDataProcessTypeManager $personnalDataProcessTypeManager, PersonnalDataRegisterManager $personnalDataRegisterManager)
    {
        $this->em = $em;
        $this->personnalDataProcessTypeManager = $personnalDataProcessTypeManager;
        $this->personnalDataRegisterManager = $personnalDataRegisterManager;
    }
    public function getPersonnalDataProcessesByType(string $type): array
    {
        $personnalDataProcesses = [];
        $personnalDataProcessType = $this->personnalDataProcessTypeManager->getPersonnalDataProcessType($type);
        if($personnalDataProcessType)
        {
            $personnalDataProcesses = $this->em->getRepository(PersonnalDataProcess::class)->findBy([
                'personnalDataProcessType' => $personnalDataProcessType,
            ]);
        }
        return $personnalDataProcesses;
    }
    public function makeProcessByType(string $type, string $description=''): PersonnalDataProcess
    {
        $personnalDataProcessType = $this->personnalDataProcessTypeManager->makePersonnalDataProcessType($type, $description);

        $personnalDataProcess = new PersonnalDataProcess();
        $personnalDataProcess->setPersonnalDataProcessType($personnalDataProcessType);
        if ($description) {
            $personnalDataProcess->setComment($description);
        }
        $this->em->persist($personnalDataProcess);
        return $personnalDataProcess;
    }
    public function addPersonnalDataFromEntity(PersonnalDataProcess $process, $entity)
    {
        $personnalDataRegisters = $this->personnalDataRegisterManager->makeAllPersonnalDataRegistersFromEntity($entity);
        if(null === $personnalDataRegisters) return;
        foreach ($personnalDataRegisters as $personnalDataRegister) {
            $process->addPersonnalDataRegister($personnalDataRegister);
        }
    }
}
