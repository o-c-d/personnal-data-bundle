<?php

namespace Ocd\PersonnalDataBundle\Event;

use Ocd\PersonnalDataBundle\Entity\PersonnalDataTransport;
use Symfony\Contracts\EventDispatcher\Event;

/** Event dispatched by Dev where needed */
class CollectPersonnalDataEvent extends Event
{
    public const NAME = 'ocd_personnal_data.collect_personnal_data';

    /** PersonnalDataTransport */
    protected $transport;

    public function __construct(PersonnalDataTransport $transport)
    {
        $this->transport = $transport;
    }

}