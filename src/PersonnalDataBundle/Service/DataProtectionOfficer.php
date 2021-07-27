<?php

namespace Ocd\PersonnalDataBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Ocd\PersonnalDataBundle\Annotation\AnnotationManager;
use Ocd\PersonnalDataBundle\Annotation\PersonnalDataReceipt;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataConsent;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataProcess;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataProvider;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataRegister;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataTransport;
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
        $this->subscribeToDoctrine = $subscribeToDoctrine;
        $this->doctrineDeclareTransports = $doctrineDeclareTransports;
        $this->em = $em;
        $this->annotationManager = $annotationManager;
        $this->personnalDataProcessManager = $personnalDataProcessManager;
        $this->personnalDataProviderManager = $personnalDataProviderManager;
        $this->personnalDataRegisterManager = $personnalDataRegisterManager;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function getTransportByType($type)
    {
        if(!isset($this->transports[$type]))
        {
            $this->transports[$type] = new PersonnalDataTransport();
            $this->transports[$type]->setType($type);
        }
        return $this->transports[$type];
    }

    /**
     * An event warned us we are collecting Personnal Data
     * Initialize Transport and Provider
     *
     * @param PersonnalDataTransport $transport
     * @param array $personnalDatas
     * @param PersonnalDataProvider|null $source
     * @return void
     */
    public function collectEvent(PersonnalDataTransport $transport, PersonnalDataProvider $provider, array $personnalDatas=[]): void
    {
        $this->updateTransport($transport, $personnalDatas, $provider);
    }

    /**
     * Database has stored some personnal data, add it to current collect transport
     *
     * @param [type] $entity
     * @param PersonnalDataTransport|null $transport
     * @param PersonnalDataProvider|null $provider
     * @return void
     */
    public function collect($entity): void
    {
        if($this->doctrineDeclareTransports)
        {
            $transport = $this->getTransportByType(PersonnalDataTransport::TYPE_COLLECT);
        }
        
        if(isset($this->transports[PersonnalDataTransport::TYPE_COLLECT]))
        {
                
            $personnalDatas = $this->annotationManager->getPersonnalDataFromEntity($entity);
            if(null !== $personnalDatas)
            {
                foreach($personnalDatas['fields'] as $fieldName => $annotation)
                {
                    $personnalDataRegister = $this->personnalDataRegisterManager->makePersonnalDataRegisterFromEntity($entity, $fieldName);
                    // TODO: check if value is not null or default or has been modified
                    $transport->addPersonnalDataRegister($personnalDataRegister);
                }
            }
            $this->transports[PersonnalDataTransport::TYPE_COLLECT] = $transport;
        }
    }

    public function exposeEvent(PersonnalDataTransport $transport, PersonnalDataProvider $provider, array $personnalDatas=[]): void
    {
        // if (null === $transport) {
        //     $transport = $this->getTransportByType(PersonnalDataTransport::TYPE_EXPOSE);
        // }
        $this->updateTransport($transport, $personnalDatas, $provider);
    }

    public function expose($entity): void
    {
        if ($this->doctrineDeclareTransports) {
            $transport = $this->getTransportByType(PersonnalDataTransport::TYPE_EXPOSE);
        }
        if(isset($this->transports[PersonnalDataTransport::TYPE_EXPOSE]))
        {
            $personnalDatas = $this->annotationManager->getPersonnalDataFromEntity($entity);
            if (null !== $personnalDatas) {
                foreach ($personnalDatas['fields'] as $fieldName => $annotation) {
                    $personnalDataRegister = $this->personnalDataRegisterManager->makePersonnalDataRegisterFromEntity($entity, $fieldName);
                    // TODO: check if value is not null or default
                    $transport->addPersonnalDataRegister($personnalDataRegister);
                }
            }
            $transportType = $transport->getType();
            $this->transports[$transportType] = $transport;
        }
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
        $this->updateTransport($transport, $personnalDatas, $destination);
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
        // revoke all consent ?
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

    public function hasProviderAgreedToProcess($provider, $process, $resonnalDatas=[]): bool
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
    }

    public function declareAllPersonnalDataFromEntity($entity, $withConsent=false)
    {
        $entityName = ClassUtils::getClass($entity);
        $annotationEntity = $this->annotationManager->getPersonnalDataFromEntity($entityName);
        /** @var PersonnalDataReceipt $personnalDataReceiptAnnotation */
        $personnalDataReceiptAnnotation = $annotationEntity['annotation'];
        if(!$personnalDataReceiptAnnotation->isPersonnalDataProvider())
        {
            // PersonnalData can only be declared by a PersonnalDataProvider
            return;
        }
        $entityMetaData = $this->em->getClassMetadata($entityName);
        $entityId = $this->propertyAccessor->getValue($entity, $entityMetaData->getSingleIdentifierFieldName());
        $personnalDataProvider = $this->personnalDataProviderManager->makeProviderByEntity($entity);
        $transportType = PersonnalDataTransport::TYPE_IMPORT."-".$entityName."-".$index;
        $personnalDataTransport = $this->getTransportByType($transportType);
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
        $annotationData = $this->annotationManager->getPersonnalDataFromEntity($entity);
        /** @var PersonnalDataReceipt $personnalDataReceiptAnnotation*/
        $personnalDataReceiptAnnotation = $annotationData['annotation'];
        $cascadeTo = $personnalDataReceiptAnnotation->getCascadeTo();
        foreach ($cascadeTo as $className => $relation)
        {
            $mappedRelation = array_map(function($fieldName) use($propertyAccessor, $entity) {
                $metaCar = $this->em->getClassMetadata(ClassUtils::getClass($entity));
                if(!$metaCar->hasField($fieldName))
                {
                    return $fieldName;
                }
                return $this->propertyAccessor->getValue($entity, $fieldName);
            }, $relation);
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

    public function updateTransport(PersonnalDataTransport $transport, array $personnalDatas=[], ?PersonnalDataProvider $provider=null)
    {
        /** @var PersonnalDataRegister $personnalData */
        foreach ($personnalDatas as $personnalData) {
            if($personnalData instanceof PersonnalDataRegister)
            {
                $transport->addPersonnalData($personnalData);
            }
        }
        if (null !== $provider) {
            $transport->setPersonnalDataProvider($provider);
        }
        $transportType = $transport->getType();
        $this->transports[$transportType] = $transport;

    }

    public function persistTransportByType($transportType)
    {
        if(isset($this->transports[$transportType]))
        {
            $this->em->persist($this->transports[$transportType]);
        }
        $this->em->flush();
    }

    public function persistTransports()
    {
        /** @var PersonnalDataTransport $transport */
        foreach($this->transports as $type => $transport)
        {
            $this->em->persist($transport);
        }
        $this->em->flush();
    }
}