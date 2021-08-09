<?php

namespace Ocd\PersonnalDataBundle\Exception;

use Ocd\PersonnalDataBundle\Entity\PersonnalDataRegister;

class PersonnalDataException extends \Exception implements PersonnalDataExceptionInterface
{
    protected PersonnalDataRegister $personnalData;

    public function __construct(PersonnPersonnalDataRegisteralDataRegiter $personnalData, string $message=null)
    {
        $this->personnalData = $personnalData;
        $this->message = $message;
    }

    /**
     * Get the value of personnalData
     */ 
    public function getPersonnalData(): PersonnalDataRegister 
    {
        return $this->personnalData;
    }

    /**
     * Set the value of personnalData
     *
     * @return  self
     */ 
    public function setPersonnalData(PersonnalDataRegister $personnalData)
    {
        $this->personnalData = $personnalData;

        return $this;
    }
}


