<?php

namespace Ocd\PersonnalDataBundle\Annotation;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Column;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\Annotation;

class AnnotationDiscovery
{

    private EntityManagerInterface $entityManager;

    private Reader $annotationReader;

    private array $annotations;

    /**
     * @var array
     */
    private $personnalDatas = [];

    public function __construct(EntityManagerInterface $entityManager, Reader $annotationReader)
    {
        $this->entityManager = $entityManager;
        $this->annotationReader = $annotationReader;
    }


    public function getAllEntitiesAnnotations() {
        $data = [];
        $managedEntities = $this->entityManager->getMetadataFactory()->getAllMetadata();
        /** @var ClassMetadata $managedEntity */
        foreach ($managedEntities as $managedEntity) {
            // 
            $entityClassName = $managedEntity->getName();
            $reflection = $managedEntity->getReflectionClass();
            $personnalDataReceiptAnnotation = $this->annotationReader->getClassAnnotation($reflection, PersonnalDataReceipt::class);

            if(!isset($data[$entityClassName]) && null !== $personnalDataReceiptAnnotation) {
                $data[$entityClassName] = [
                    'annotation' => $personnalDataReceiptAnnotation,
                    'fields' => [],
                ];
                $reflectionProperties = $managedEntity->getReflectionProperties();
                /** @var \ReflectionProperty $refProperty */
                foreach ($reflectionProperties as $refProperty) {
                    // 
                    $propertyName = $refProperty->getName();
                    $propertyType = $refProperty->getType() ? $refProperty->getType()->getName() : null;
                    if(null === $propertyType)
                    {
                        $doctrinePropertyAnnotation = $this->annotationReader->getPropertyAnnotation($refProperty, Column::class);
                        /** @var $doctrinePropertyAnnotation Annotation */
                        if($doctrinePropertyAnnotation instanceof Column)
                        {
                            $propertyType = $doctrinePropertyAnnotation->type;
                        }
                    }
                    $propertyAnnotation = $this->annotationReader->getPropertyAnnotation($refProperty, PersonnalData::class);
                    if(null !== $propertyAnnotation)
                    {
                        $data[$entityClassName]['fields'][$propertyName] = [
                            "type" => $propertyType,
                            "annotation" => $propertyAnnotation,
                        ];
                    }
                }
            }


       }
        return $data;

    }
}