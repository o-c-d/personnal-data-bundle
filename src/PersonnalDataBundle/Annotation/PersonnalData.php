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
    const CATEGORIES = [SELF::IDENTITY, SELF::PERSONNAL, SELF::WORKING, SELF::BUSINESS, SELF::LOCATION, SELF::JUDICIAL, SELF::SENSITIVE ];
    const IDENTITY = 'IDENTITY'; // Name, email, passwords...
    const PERSONNAL = 'PERSONNAL'; // married/single, hobbies...
    const WORKING = 'WORKING'; // professionnal context, company name, responsabilities...
    const BUSINESS = 'BUSINESS'; // salary, and economic datas...
    const LOCATION = 'LOCATION'; // address, country, IP, gps coordinates...
    const JUDICIAL = 'JUDICIAL'; // judicial records, sentences, citations...
    const SENSITIVE = 'SENSITIVE'; // sexual orientation, political conviction...

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
     * @Enum({"IDENTITY", "PERSONNAL", "WORKING", "ECONOMIC", "LOCATION", "JUDICIAL", "SENSITIVE"})
     * 
     */
    public $category;

    /**
     *
     * @var string
     */
    public $purpose;


    /**
     * This entity can be a PersonnalDataProvider
     *
     * @var bool
     */
    public $isPersonnalDataProviderActivityDateTime=false;



///////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////                             ANONYMIZATION                                   ///////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * Method used for archiving
     *
     * @var string
     */
    public $intermediateArchivingMethod="Ocd\PersonnalDataBundle\Archiver\Lazy::return";

    /**
     * Method used for anonymizaiton
     *
     * @var string
     */
    public $finalArchivingMethod="Ocd\PersonnalDataBundle\Archiver\Lazy::return";

    /**
     * Callable functions after archiving
     *
     * @var string
     */
    public $postIntermediateArchiving;

    /**
     * Callable functions after anonymizaiton
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

    /**+
     * @Required
     *
     * @var string
     */
    public $intermediateConservationDuration;

    /**
     * Field Name of the DateTime field from which to start conservation duration in active database
     * @Required
     * 
     *
     * @var string
     */
    public $activeConservationDurationStartFromField;

    /**
     * Field Name of the DateTime field from which to start conservation duration in intermediate archive mode
     * @Required
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
