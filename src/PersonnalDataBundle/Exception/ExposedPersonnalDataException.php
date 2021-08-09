<?php

namespace Ocd\PersonnalDataBundle\Exception;

use Ocd\PersonnalDataBundle\Entity\PersonnalDataRegister;

class ExposedPersonnalDataException extends PersonnalDataException
{

    public function __construct(PersonnalDataRegister $personnalData, string $message=null)
    {
        parent::__construct($personnalData, $message);
    }
}