<?php

namespace Ocd\PersonnalDataBundle\Event\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Ocd\PersonnalDataBundle\Service\DataProtectionOfficer;

class DoctrineSubscriber implements EventSubscriber
{

    private DataProtectionOfficer $dataProtectionOfficer;
    private bool $subscribeToDoctrine=false;

    public function __construct(DataProtectionOfficer $dataProtectionOfficer, $subscribeToDoctrine=false)
    {
        $this->dataProtectionOfficer = $dataProtectionOfficer;
        $this->subscribeToDoctrine = $subscribeToDoctrine;
    }

    public function getSubscribedEvents(): array
    {
        if(!$this->subscribeToDoctrine)
        {
            return [];
        }
        return [
            Events::postLoad,
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
        ];
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        // check if has PersonnalData
        $this->dataProtectionOfficer->expose($entity);
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $this->dataProtectionOfficer->collect($entity);
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        // dispose ?
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        // TODO: check if personnal data is updated
        // /* @var $em Doctrine\ORM\EntityManager */
        // $em = $args->getObjectManager();
        // $uow = $em->getUnitOfWork();
        // $uow->getEntityChangeSet($entity)
        $entity = $args->getObject();
        $this->dataProtectionOfficer->collect($entity);

    }
}