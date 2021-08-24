<?php

namespace Ocd\PersonnalDataBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class PersonnalDataReceipt
{
    /**
     * @Required
     *
     * @var string
     */
    public $name;

    /**
     * Unique name for Entity
     * @Required
     *
     * @var string
     */
    public $description;

    /**
     * 
     * @Required
     * cascadeTo = [
     *      'Path\To\Child\Entity' => [
     *          'childEntityFieldName' => 'parentEntityFieldName'
     *      ]
     * ]
     *
     * @var array
     */
    public $cascadeTo=[];

    /**
     * This entity can be a PersonnalDataProvider
     *
     * @var bool
     */
    public $isPersonnalDataProvider=false;


    /**
     * Field not null when entity is soft-deleted
     *
     * @var string
     */
    public $softDeletedBy=null;


    /**
     * Get this entity can be a PersonnalDataProvider
     *
     * @return  bool
     */ 
    public function isPersonnalDataProvider()
    {
        return $this->isPersonnalDataProvider;
    }

    /**
     * Get the value of name
     *
     * @return  string
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param  string  $name
     *
     * @return  self
     */ 
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get unique name for Entity
     *
     * @return  string
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set unique name for Entity
     *
     * @param  string  $description  Unique name for Entity
     *
     * @return  self
     */ 
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get ]
     *
     * @return  array
     */ 
    public function getCascadeTo()
    {
        return $this->cascadeTo;
    }

    /**
     * Set ]
     *
     * @param  array  $cascadeTo  ]
     *
     * @return  self
     */ 
    public function setCascadeTo(array $cascadeTo)
    {
        $this->cascadeTo = $cascadeTo;

        return $this;
    }

    /**
     * Get this entity can be a PersonnalDataProvider
     *
     * @return  bool
     */ 
    public function getIsPersonnalDataProvider()
    {
        return $this->isPersonnalDataProvider;
    }

    /**
     * Set this entity can be a PersonnalDataProvider
     *
     * @param  bool  $isPersonnalDataProvider  This entity can be a PersonnalDataProvider
     *
     * @return  self
     */ 
    public function setIsPersonnalDataProvider(bool $isPersonnalDataProvider)
    {
        $this->isPersonnalDataProvider = $isPersonnalDataProvider;

        return $this;
    }

    /**
     * Get field not null when entity is soft-deleted
     *
     * @return  string
     */ 
    public function getSoftDeletedBy()
    {
        return $this->softDeletedBy;
    }

    /**
     * Set field not null when entity is soft-deleted
     *
     * @param  string  $softDeletedBy  Field not null when entity is soft-deleted
     *
     * @return  self
     */ 
    public function setSoftDeletedBy(?string $softDeletedBy=null)
    {
        $this->softDeletedBy = $softDeletedBy;

        return $this;
    }
}