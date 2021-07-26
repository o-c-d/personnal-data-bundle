<?php

namespace Ocd\PersonnalDataBundle\Manager;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataRegister;
use Ocd\PersonnalDataBundle\Annotation\AnnotationManager;


class PersonnalDataRegisterManager
{
    private EntityManagerInterface $em;
    private AnnotationManager $annotationManager;

    public function __construct(AnnotationManager $annotationManager, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->annotationManager = $annotationManager;
    }

    /**
     * Undocumented function
     *
     * @param [type] $entity
     * @return array PersonnalDataRegister[]
     */
    public function makeAllPersonnalDataRegistersFromEntity($entity): array
    {
        $personnalDatas = [];
        $entityName = ClassUtils::getClass($entity);
        $entityId = $entity->getId();
        $personnalDataAnnotations = $this->annotationManager->getPersonnalDataFromEntity($entity);
        // TODO: check if entity has annotation
        // $personnalDataReceiptAnnotation = $personnalDataAnnotations['annotation'];
        if(isset($personnalDataAnnotations['fields']))
        {
            foreach($personnalDataAnnotations['fields'] as $fieldName => $fieldData)
            {
                $personnalDatas[] = $this->makePersonnalDataFromEntity($entity, $fieldName);
            }
        }
        return $personnalDatas;
    }

    public function makePersonnalDataRegisterFromEntity($entity, $fieldName): ?PersonnalDataRegister
    {
        // check if this field is desclared as personnal data annotation
        if(!$this->annotationManager->isPersonnalData(ClassUtils::getClass($entity), $fieldName))
        {
            return null;
        }
        $personnalDataRegister = $this->getPersonnalDataRegisterFromEntity($entity, $fieldName);
        if($personnalDataRegister)
        {
            return $personnalDataRegister;
        }
        $entityName = ClassUtils::getClass($entity);
        $entityId = $entity->getId();
        $newPersonnalDataRegister = new PersonnalDataRegister();
        $newPersonnalDataRegister->setEntityName($entityName);
        $newPersonnalDataRegister->setEntityId($entityId);
        $newPersonnalDataRegister->setFieldName($fieldName);
        $this->em->persist($newPersonnalDataRegister);
        return $newPersonnalDataRegister;
    }

    public function getPersonnalDataRegisterFromEntity($entity, $fieldName): ?PersonnalDataRegister
    {
        $entityName = ClassUtils::getClass($entity);
        $entityId = $entity->getId();
        $personnalDataRegister = $this->em->getRepository(PersonnalDataRegister::class)->findOneBy([
            'entityName' => $entityName,
            'entityId' => $entityId,
            'fieldName' => $fieldName,
        ]);
        return $personnalDataRegister;
    }
}