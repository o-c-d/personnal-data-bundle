<?php

namespace Ocd\PersonnalDataBundle\Annotation;

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

    public function getPersonnalDataFromEntity(string $entityClassPath): ?array
    {
        if(isset($this->annotations[$entityClassPath]) && isset($this->annotations[$entityClassPath])) {
            return $this->annotations[$entityClassPath];
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