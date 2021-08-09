<?php

namespace Ocd\PersonnalDataBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/** Event dispatched by DoctrineSubscriber */
class ExposedPersonnalDataEntityEvent extends Event
{
    public const NAME = 'ocd_personnal_data.exposed_personnal_data_entity';

    /** Object */
    protected $entity;

    protected $context=[];

    public function __construct($entity, $context=[])
    {
        $this->entity = $entity;
        $this->context = $context;
    }



    /**
     * Get the value of entity
     */ 
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Get the value of context
     */ 
    public function getContext()
    {
        return $this->context;
    }
}