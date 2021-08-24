<?php

namespace Ocd\PersonnalDataBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Util\ClassUtils;
use Ocd\PersonnalDataBundle\Annotation\AnnotationManager;
use Ocd\PersonnalDataBundle\Annotation\PersonnalDataReceipt;
use Ocd\PersonnalDataBundle\DataCollector\PersonnalDataCollector;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataConsent;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataProcess;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataProvider;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataRegister;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataTransport;
use Ocd\PersonnalDataBundle\Exception\ExposedPersonnalDataException;
use Ocd\PersonnalDataBundle\Manager\PersonnalDataProcessManager;
use Ocd\PersonnalDataBundle\Manager\PersonnalDataProviderManager;
use Ocd\PersonnalDataBundle\Manager\PersonnalDataRegisterManager;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class DataProtectionOfficer
{

    /**
     * list of transports during whole execution
     *
     * @var array PersonnalDataTransport[]
     */
    private array $transports = [];

    /**
     * list of processes during whole execution
     *
     * @var array PersonnalDataProcess[]
     */
    private array $processes = [];

    /**
     * list of personnal data collected during whole execution
     *
     * @var array 
     */
    private array $collected = [];

    /**
     * list of personnal data exposed during whole execution
     *
     * @var array 
     */
    private array $exposed = [];

    /**
     * Lisdt of personnal data in transport or processes not seen in exposed or collected
     */
    private array $errors = [];

    /**
     * List of personnal data exposed or collected but not in transports or processes
     */
    private array $violations = [];

    private bool $subscribeToDoctrine = false;
    private bool $doctrineDeclareTransports = false;
    public bool $isRequestContext = false;
    public bool $isConsoleContext = false;

    private EntityManagerInterface $em;
    private AnnotationManager $annotationManager;
    private PersonnalDataProcessManager $personnalDataProcessManager;
    private PersonnalDataProviderManager $personnalDataProviderManager;
    private PersonnalDataRegisterManager $personnalDataRegisterManager;
    private PropertyAccessor $propertyAccessor;



    public function __construct(
        bool $subscribeToDoctrine, 
        bool $doctrineDeclareTransports, 
        EntityManagerInterface $em, 
        AnnotationManager $annotationManager, 
        PersonnalDataProcessManager $personnalDataProcessManager,
        PersonnalDataProviderManager $personnalDataProviderManager,
        PersonnalDataRegisterManager $personnalDataRegisterManager
        )
    {
        $this->transports = [];
        $this->processes = [];
        $this->collected = [];
        $this->exposed = [];
        $this->subscribeToDoctrine = $subscribeToDoctrine;
        $this->doctrineDeclareTransports = $doctrineDeclareTransports;
        $this->em = $em;
        $this->annotationManager = $annotationManager;
        $this->personnalDataProcessManager = $personnalDataProcessManager;
        $this->personnalDataProviderManager = $personnalDataProviderManager;
        $this->personnalDataRegisterManager = $personnalDataRegisterManager;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * An event warned us we are collecting Personnal Data
     * Initialize Transport and Provider
     *
     * @param PersonnalDataTransport $transport
     * @return void
     */
    public function addTransport(PersonnalDataTransport $transport): void
    {
        $this->transports[] = $transport;
    }

    /**
     * A process using PersonnalData is running
     *
     * @param PersonnalDataProcess $process
     * @return void
     */
    public function addProcess(PersonnalDataProcess $process): void
    {
        $this->processes[] = $process;
    }

    /**
     * Database has stored some personnal data, add it to current collected datas
     *
     * @param [type] $entity
     * @param array $context
     * @return void
     */
    public function collect($entity, $context=[]): void
    {
        $personnalDatas = $this->personnalDataRegisterManager->makeAllPersonnalDataRegistersFromEntity($entity);
        $this->collected = self::makeContextCollection($this->collected, $personnalDatas, $context);
    }


    static public function makeContextCollection($collection, $personnalDatas, $context)
    {
        foreach ($personnalDatas as $personnalData) {
            $entityName = $personnalData->getEntityName();
            if (!isset($collection[$entityName])) {
                $collection[$entityName] = [];
            }
            $entityId = $personnalData->getEntityId();
            if (!isset($collection[$entityName][$entityId])) {
                $collection[$entityName][$entityId] = [];
            }
            $fieldName = $personnalData->getFieldName();
            if (!isset($collection[$entityName][$entityId][$fieldName])) {
                $collection[$entityName][$entityId][$fieldName] = [];
            }
            $collection[$entityName][$entityId][$fieldName][] = $context;
        }
        return $collection;
    }


    /**
     * Database has loaded some personnal data, add it to current exposed datas
     *
     * @param [type] $entity
     * @param array $context
     * @return void
     */
    public function expose($entity, $context=[]): void
    {
        $personnalDatas = $this->personnalDataRegisterManager->makeAllPersonnalDataRegistersFromEntity($entity);
        $this->exposed = self::makeContextCollection($this->exposed, $personnalDatas, $context);
    }

    public function consentEvent(PersonnalDataProvider $destination, PersonnalDataProcess $process, array $personnalDatas = [])
    {
        $consentPersonnalData = new PersonnalDataConsent();
        $consentPersonnalData->setPersonnalDataProvider($destination);
        $consentPersonnalData->setPersonnalDataProcess($process);
        /** @var PersonnalDataRegister $personnalData */
        foreach ($personnalDatas as $personnalData) {
            if($personnalData instanceof PersonnalDataRegister)
            {
                $consentPersonnalData->addPersonnalData($personnalData);
            }
        }
        $this->em->persist($consentPersonnalData);
        $this->em->flush();
    }

    public function exportEvent(PersonnalDataProvider $destination, PersonnalDataTransport $transport, array $personnalDatas = [])
    {
        // if (null === $transport) {
        //     $transport = $this->getTransportByType(PersonnalDataTransport::TYPE_EXPORT);
        // }
        // $this->updateTransport($transport, $personnalDatas, $destination);
    }

    public function disposeEvent(array $personnalDatas = [])
    {
        // Save information that entity corresponding to PersonnalDataRegister has been deleted
    }

    public function intermediateArchiveEvent(array $personnalDatas = [])
    {
        foreach($personnalDatas as $personnalData)
        {
            $personnalData->setIntermediateArchivedAt(new DateTime());
            $this->em->persist($personnalData);
        }
        $this->em->flush();
        // expire all consent ?
    }

    public function finalArchiveEvent(array $personnalDatas = [])
    {
        foreach ($personnalDatas as $personnalData) {
            $personnalData->setFinalArchivedAt(new DateTime());
            $this->em->persist($personnalData);
        }
        $this->em->flush();
    }

    /**
     * data loaded by doctrine during console context ??
     *
     * @param [type] $entity
     * @return void
     */
    public function process($entity): void
    {
        // Check for consent ??
    }

    public function hasProviderAgreedToProcess($provider, $process, $personnalDatas=[]): bool
    {

    }

    public function declareAllPersonnalDataInDatabase($withConsent=false): array
    {
        $annotations = $this->annotationManager->getAllEntitiesAnnotations();
        foreach($annotations as $entityName => $entityData)
        {
            /** @var PersonnalDataReceipt $personnalDataReceiptAnnotation*/
            $personnalDataReceiptAnnotation = $entityData['annotation'];
            if($personnalDataReceiptAnnotation->isPersonnalDataProvider())
            {
                $allEntityData = $this->em->getRepository($entityName)->findAll();
                foreach ($allEntityData as $index => $entity) {
                    // Todo: check if this personal data is already present in personnalDataRegister
                    $this->declareAllPersonnalDataFromEntity($entity);
                }
            }
        }
        // TODO: Check for undeclared personnal data (not linked to a provider)
        $orphans = [];
        foreach($annotations as $entityName => $entityData)
        {
            $orphans[$entityName]=[];
            $allEntityData = $this->em->getRepository($entityName)->findAll();
                foreach ($allEntityData as $index => $entity) {
                    // find entity in PersonnalDataRegister
                    $declaredFields = $this->em->getRepository(PersonnalDataRegister::class)->findByEntity($entity);
                    $orphans[$entityName][$entity->getId()]=[];
                    foreach($entityData['fields'] as $field)
                    {
                        if(!in_array($field, $declaredFields)) {
                            $orphans[$entityName][$entity->getId()][] = $field;
                        }
                    }
                }
        }
        return $orphans;
    }

    public function declareAllPersonnalDataFromEntity($entity, $withConsent=false)
    {
        $entityName = ClassUtils::getClass($entity);
        /** @var PersonnalDataReceipt $personnalDataReceiptAnnotation */
        $personnalDataReceiptAnnotation = $this->annotationManager->getAnnotationFromTable($entityName);
        if(!$personnalDataReceiptAnnotation->isPersonnalDataProvider())
        {
            // PersonnalData can only be declared by a PersonnalDataProvider
            return;
        }
        $entityMetaData = $this->em->getClassMetadata($entityName);
        $entityId = $this->propertyAccessor->getValue($entity, $entityMetaData->getSingleIdentifierFieldName());
        $personnalDataProvider = $this->personnalDataProviderManager->makeProviderByEntity($entity);
        $transportType = PersonnalDataTransport::TYPE_IMPORT."-".$entityName."-".$entityId;
        $personnalDataTransport = new PersonnalDataTransport($transportType);
        $personnalDataTransport->setType($transportType);
        $personnalDataTransport->setPersonnalDataProvider($personnalDataProvider);
        $personnalDataConsent = null;
        if($withConsent)
        {
            $personnalDataProcess = $this->personnalDataProcessManager->makePersonnalDataProcessByConstantCode(PersonnalDataProcess::IMPORT);
            $personnalDataConsent = new PersonnalDataConsent();
            $personnalDataConsent->setPersonnalDataProvider($personnalDataProvider);
            $personnalDataConsent->setPersonnalDataProcess($personnalDataProcess);
        }
        $this->recursiveDeclaration($entity, $personnalDataTransport, $personnalDataConsent);
        if($withConsent)
        {
            $this->em->persist($personnalDataConsent);
        }
        // TODO: persist and remove personnalDataTransport from transports list ?
    }

    public function recursiveDeclaration($entity, PersonnalDataTransport $personnalDataTransport, ?PersonnalDataConsent $personnalDataConsent)
    {
        $declaredEntities = 0;
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        // Fields
        $personnalDatas = $this->personnalDataRegisterManager->makeAllPersonnalDataRegistersFromEntity($entity);
        foreach ($personnalDatas as $personnalData)
        {
            $personnalDataTransport->addPersonnalData($personnalData);
            if(null !== $personnalDataConsent)
            {
                $personnalDataConsent->addPersonnalData($personnalData);
            }
        }
        // Cascade
        $entityName = ClassUtils::getClass($entity);
        /** @var PersonnalDataReceipt $personnalDataReceiptAnnotation*/
        $personnalDataReceiptAnnotation = $this->annotationManager->getAnnotationFromTable($entityName);
        $cascadeTo = $personnalDataReceiptAnnotation->getCascadeTo();
        foreach ($cascadeTo as $indexCascade => $cascade)
        {
            foreach($cascade as $className => $relations)
            {
                $mappedRelation = array_map(function($fieldName) use($propertyAccessor, $entity) {
                    $metaCar = $this->em->getClassMetadata(ClassUtils::getClass($entity));
                    if(!$metaCar->hasField($fieldName))
                    {
                        return $fieldName;
                    }
                    return $this->propertyAccessor->getValue($entity, $fieldName);
                }, $relations);
            }
            $childs = $this->em->getRepository($className)->findBy($mappedRelation);
            foreach ($childs as $childEntity)
            {
                $this->recursiveDeclaration($childEntity, $personnalDataTransport, $personnalDataConsent);
            }
        }
    }


    public function anonymizePersonnalDataFromEntity($entity)
    {
        //...

    }


    public function anonymizePersonnalDataFromProvider(PersonnalDataProvider $provider)
    {
        $personnalDatas = $this->em->getRepository(PersonnalDataRegister::class)->findByPersonnalDataProvider($provider);
        foreach($personnalDatas as $personnalData)
        {
            $className = $personnalData->getEntityName();
            $fieldName = $personnalData->getFieldName();
            $entityId = $personnalData->getEntityId();
            $entity = $this->em->getRepository($className)->find($entityId);
            $value = $this->propertyAccessor->getValue($entity, $fieldName);
            $annotation = $this->annotationManager->getAnnotationFromField($className, $fieldName);
            //...
        }
    }

    public function exportPersonnalDataFromProvider(PersonnalDataProvider $provider)
    {
        $exportData = [];
        $personnalDatas = $this->em->getRepository(PersonnalDataRegister::class)->findByPersonnalDataProvider($provider);
        foreach($personnalDatas as $personnalData)
        {
            $className = $personnalData->getEntityName();
            $fieldName = $personnalData->getFieldName();
            $entityId = $personnalData->getEntityId();
            $entity = $this->em->getRepository($className)->find($entityId);
            $value = $this->propertyAccessor->getValue($entity, $fieldName);
            if(!isset($exportData[$className])) $exportData[$className] = [];
            if(!isset($exportData[$className][$entityId])) $exportData[$className][$entityId] = [];
            if(!isset($exportData[$className][$entityId][$fieldName])) $exportData[$className][$entityId][$fieldName] = $value;
        }
        return $exportData;
    }

    /**
     * WiP
     *
     * @return void
     */

     public function timeArchiver(): void
    {
        $annotations = $this->annotationManager->getAllEntitiesAnnotations();
        foreach ($annotations as $entityName => $entityData) {
            /** @var PersonnalDataReceipt $personnalDataReceiptAnnotation*/
            $personnalDataReceiptAnnotation = $entityData['annotation'];
            foreach($entityData['fields'] as $fieldName => $personnalData)
            {

            }

        }

    }


    public function getPersonnalDataFromDbCollection($dbCollection=[])
    {
        $personnalDatas = [];
        foreach ($dbCollection as $entityName => $entity) {
            foreach ($entity as $entityId => $fields) {
                foreach ($fields as $field) {
                    foreach($field as $fieldName => $context)
                    {
                        $personnalData = $this->em->getRepository(PersonnalDataRegister::class)->findOneBy([
                            'entityName' => $entityName,
                            'entityId' => $entityId,
                            'fieldName' => $fieldName,
                        ]);
                        if($personnalData)
                        {
                            $personnalDatas[] = $personnalData;
                        }
                    }
                }
            }
        }
        return $personnalDatas;
    }

    public function validation()
    {
        $personnalDataFromEvents = array_merge(
            PersonnalDataCollector::getPersonnalDataRegitersFromEvent($this->transports),
            PersonnalDataCollector::getPersonnalDataRegitersFromEvent($this->processes),
        );
        $personnalDatasFromDb = array_merge(
            $this->getPersonnalDataFromDbCollection($this->collected),
            $this->getPersonnalDataFromDbCollection($this->exposed),
        );
        foreach($personnalDatasFromDb as $personnalDataFromDb)
        {
            if(
                !isset($personnalDataFromEvents[$personnalDataFromDb->getEntityName()])
                || !isset($personnalDataFromEvents[$personnalDataFromDb->getEntityName()][$personnalDataFromDb->getEntityId()])
                || !isset($personnalDataFromEvents[$personnalDataFromDb->getEntityName()][$personnalDataFromDb->getEntityId()][$personnalDataFromDb->getFieldName()])
            )
            {
                // Personnal Data used without declaration
                $this->violations[] = $personnalDataFromDb;
                // throw new ExposedPersonnalDataException($personnalData);
            }
        }
        foreach ($personnalDataFromEvents as $personnalDataFromEvent) {
            if (
                !isset($personnalDatasFromDb[$personnalDataFromEvent->getEntityName()])
                || !isset($personnalDatasFromDb[$personnalDataFromEvent->getEntityName()][$personnalDataFromEvent->getEntityId()])
                || !isset($personnalDatasFromDb[$personnalDataFromEvent->getEntityName()][$personnalDataFromEvent->getEntityId()][$personnalDataFromEvent->getFieldName()])
            ) {
                // Personnal Data declared without usage
                $this->errors[] = $personnalDataFromEvent;
            }
        }
        $this->em->flush();
    }

    /**
     * Get personnalDataTransport[]
     *
     * @return  array
     */ 
    public function getTransports()
    {
        return $this->transports;
    }

    /**
     * Get personnalDataProcess[]
     *
     * @return  array
     */ 
    public function getProcesses()
    {
        return $this->processes;
    }

    /**
     * Get personnalDataRgister[]
     *
     * @return  array
     */ 
    public function getCollected()
    {
        return $this->collected;
    }

    /**
     * Get personnalDataProcess[]
     *
     * @return  array
     */ 
    public function getExposed()
    {
        return $this->exposed;
    }
}