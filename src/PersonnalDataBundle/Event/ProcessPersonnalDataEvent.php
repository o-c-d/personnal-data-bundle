<?php

namespace Ocd\PersonnalDataBundle\Event;

use Ocd\PersonnalDataBundle\Entity\PersonnalDataProcess;
use Symfony\Contracts\EventDispatcher\Event;

class ProcessPersonnalDataEvent extends Event
{
    /** PersonnalDataProcess */
    protected $personnalDataProcess;

    public function __construct(PersonnalDataProcess $personnalDataProcess)
    {
        $this->personnalDataProcess = $personnalDataProcess;
    }

    /**
     * Get the value of personnalDataProcess
     */ 
    public function getPersonnalDataProcess()
    {
        return $this->personnalDataProcess;
    }

    /**
     * Set the value of personnalDataProcess
     *
     * @return  self
     */ 
    public function setPersonnalDataProcess($personnalDataProcess)
    {
        $this->personnalDataProcess = $personnalDataProcess;

        return $this;
    }
}
