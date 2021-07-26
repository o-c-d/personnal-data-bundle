<?php

namespace Ocd\PersonnalDataBundle\Event;

use Ocd\PersonnalDataBundle\Entity\PersonnalDataProvider;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataRegister;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataTransport;
use Symfony\Contracts\EventDispatcher\Event;

class ExposePersonnalDataEvent extends Event
{
    /** PersonnalDataProvider */
    protected $destination;

    /** PersonnalDataTransport */
    protected $transport;

    /** PersonnalDataRegister[] */
    protected $personnalDatas;

    public function __construct(PersonnalDataProvider $destination, PersonnalDataTransport $transport, array $personnalDatas = [])
    {
        $this->destination = $destination;
        $this->transport = $transport;
        $this->personnalDatas = $personnalDatas;
    }
}
