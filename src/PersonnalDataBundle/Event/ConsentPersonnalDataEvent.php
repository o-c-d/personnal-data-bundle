<?php

namespace Ocd\PersonnalDataBundle\Event;

use Ocd\PersonnalDataBundle\Entity\PersonnalDataProcess;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataProvider;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataRegister;
use Symfony\Contracts\EventDispatcher\Event;

class ConsentPersonnalDataEvent extends Event
{
    /** PersonnalDataProvider */
    protected $source;

    /** PersonnalDataProcess */
    protected $process;

    /** PersonnalDataRegister[] */
    protected $personnalDatas;

    public function __construct(PersonnalDataProvider $source, PersonnalDataProcess $process, array $personnalDatas = [])
    {
        $this->source = $source;
        $this->process = $process;
        $this->personnalDatas = $personnalDatas;
    }
}
