<?php

namespace Ocd\PersonnalDataBundle\Event;

use Ocd\PersonnalDataBundle\Entity\PersonnalDataProvider;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataTransport;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataRegister;
use Symfony\Contracts\EventDispatcher\Event;


class CollectPersonnalDataEvent extends Event
{
    public const NAME = 'ocd_personnal_data.collect_personnal_data';

    /** PersonnalDataTransport */
    protected $transport;

    /** ?PersonnalDataProvider */
    protected $source;

    /** PersonnalDataRegister[] */
    protected $personnalDatas;

    public function __construct(PersonnalDataTransport $transport, PersonnalDataProvider $source, array $personnalDatas = [])
    {
        $this->transport = $transport;
        $this->source = $source;
        $this->personnalDatas = $personnalDatas;
    }

}