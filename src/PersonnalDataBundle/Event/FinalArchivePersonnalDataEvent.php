<?php

namespace Ocd\PersonnalDataBundle\Event;

use Ocd\PersonnalDataBundle\Entity\PersonnalDataRegister;
use Symfony\Contracts\EventDispatcher\Event;

class FinalArchivePersonnalDataEvent extends Event
{
    /** PersonnalDataRegister[] */
    protected $personnalDatas;

    public function __construct(array $personnalDatas = [])
    {
        $this->personnalDatas = $personnalDatas;
    }
}
