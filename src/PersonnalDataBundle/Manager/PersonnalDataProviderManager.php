<?php

namespace Ocd\PersonnalDataBundle\Manager;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataProvider;

class PersonnalDataProviderManager
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getProviderByEntity($entity): ?PersonnalDataProvider
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $entityName = ClassUtils::getClass($entity);
        $entityId = $propertyAccessor->getValue($entity, 'id');
        $provider = $this->em->getRepository(PersonnalDataProvider::class)->findOneBy([
            'entityName' => $entityName,
            'entityId' => $entityId,
        ]);
        return $provider;
    }

    public function makeProviderByEntity($entity): PersonnalDataProvider
    {
        $provider = $this->getProviderByEntity($entity);
        if(!$provider)
        {
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            $entityName = ClassUtils::getClass($entity);
            $entityMetaData = $this->em->getClassMetadata($entityName);
            $entityId = $propertyAccessor->getValue($entity, $entityMetaData->getSingleIdentifierFieldName());
            $provider = new PersonnalDataProvider();
            $provider->setEntityName($entityName);
            $provider->setEntityId($entityId);
            $this->em->persist($provider);
        }
        return $provider;
    }
}