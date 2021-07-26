<?php

namespace Ocd\PersonnalDataBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * A property with PersonnalData annotation must belong to an entity with PersonnalDataReceipt annotation
 * @Annotation
 * @Target("PROPERTY")
 */
class PersonnalData
{
    const CATEGORIES = ['identity', 'personnal', 'working', 'business', 'location', 'judicial', 'sensitive' ];
    const IDENTITY = 'identity';
    const PERSONNAL = 'personnal';
    const WORKING = 'working';
    const BUSINESS = 'business';
    const LOCATION = 'location';
    const JUDICIAL = 'judicial';
    const SENSITIVE = 'sensitive';

    /**
     * Unique name for Personnal Data
     * @Required
     *
     * @var string
     */
    public $name;

    /**
     * Description for Personnal Data
     * @var string
     */
    public $description;

    /**
     * Category for Personnal Data
     * should belong to SELF::CATEGORIES
     * @Enum({"IDENTITY", "PERSONNAL", "WORKING", "ECONOMIC", "LOCATION"; "JUDICIAL", "SENSITIVE"})
     */
    public $category;

    /**
     *
     * @var string
     */
    public $purpose;




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
     * Prefered format method to display property value
     * @Required
     *
     * @var string
     */
    public $formatMethod;












    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getConservationDuration()
    {
        return $this->conservationDuration;
    }
}
