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


///////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////                             ANONYMIZATION                                   ///////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////



    /**
     * Field being not null when entity is intermediate archived
     * @Required
     *
     * @var string
     */
    public $intermediateArchivedBy;

    /**
     * Field being not null when entity is final archived
     *
     * @var string
     */
    public $finalArchivedBy;

    /**
     * Method used for archiving
     * @Required
     *
     * @var string
     */
    public $intermediateArchivingMethod;

    /**
     * Method used for anonymizaiton
     * @Required
     *
     * @var string
     */
    public $finalArchivingMethod;

    /**
     * Callable functions after archiving
     * @Required
     *
     * @var string
     */
    public $postIntermediateArchiving;

    /**
     * Callable functions after anonymizaiton
     * @Required
     *
     * @var string
     */
    public $postFinalArchiving;

    /**
     * @Required
     *
     * @var string
     */
    public $activeConservationDuration;

    /**
     * @Required
     *
     * @var string
     */
    public $intermediateConservationDuration;

    /**
     * Field Name of the DateTime field from which to start conservation duration in active database
     * 
     *
     * @var string
     */
    public $activeConservationDurationStartFromField;

    /**
     * Field Name of the DateTime field from which to start conservation duration in intermediate archive mode
     * 
     *
     * @var string
     */
    public $intermediateConservationDurationStartFromField;



///////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////                                 EXPORT                                      ///////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////



    /**
     * Prefered format
     * @Required
     *
     * @var string
     */
    public $format;









    public function getName()
    {
        return $this->name;
    }

    /**
     * Get this entity can be a PersonnalDataProvider
     *
     * @return  bool
     */ 
    public function isPersonnalDataProvider()
    {
        return $this->isPersonnalDataProvider;
    }
}