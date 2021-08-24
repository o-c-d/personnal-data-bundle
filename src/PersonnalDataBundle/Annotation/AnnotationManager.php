<?php

namespace Ocd\PersonnalDataBundle\Annotation;

use Doctrine\Common\Util\ClassUtils;

class AnnotationManager
{
    /**
     * @var array
     */
    private $annotations;


    public function __construct(AnnotationDiscovery $discovery)
    {
        $this->annotations = $discovery->getAllEntitiesAnnotations();
    }

    public function getAllEntitiesAnnotations()
    {
        return $this->annotations;
    }

    public function getPersonnalDataFromEntity($entity): ?array
    {
        $entityClassPath = ClassUtils::getClass($entity);
        return $this->getPersonnalDataFromEntityName($entityClassPath);
    }

    // TODO: add annotations validation e.g.: table with PersonnalDataReceipt but with no field with PersonnalData

    public function getPersonnalDataFromEntityName(string $entityClassPath): ?array
    {
        if(isset($this->annotations[$entityClassPath]) && isset($this->annotations[$entityClassPath]['fields'])) {
            $personnalDataAnnotations = [];
            foreach($this->annotations[$entityClassPath]['fields'] as $field)
            {
                $personnalDataAnnotations[] = $field['annotation'];

            }
            return $personnalDataAnnotations;
        }
        return null;
    }

    public function getAnnotationFromTable(string $entityClassPath): ?PersonnalDataReceipt
    {
        if (isset($this->annotations[$entityClassPath]) && isset($this->annotations[$entityClassPath]['annotation'])) {
            return $this->annotations[$entityClassPath]['annotation'];
        }
        return null;
    }

    public function getAnnotationFromField(string $entityClassPath, string $fieldName): ?PersonnalData
    {
        if (isset($this->annotations[$entityClassPath]) && isset($this->annotations[$entityClassPath]['fields'][$fieldName])) {
            return $this->annotations[$entityClassPath]['fields'][$fieldName];
        }
        return null;
    }

    public function hasPersonnalData(string $entityClassPath): bool
    {
        return isset($this->annotations[$entityClassPath]);
    }

    public function isPersonnalData(string $entityClassPath, string $fieldName): bool
    {
        return (isset($this->annotations[$entityClassPath]) && isset($this->annotations[$entityClassPath]['fields'][$fieldName]));
    }

}