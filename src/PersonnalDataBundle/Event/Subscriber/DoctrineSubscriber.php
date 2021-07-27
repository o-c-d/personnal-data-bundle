<?php

namespace Ocd\PersonnalDataBundle\Event\Subscriber;

use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Ocd\PersonnalDataBundle\Annotation\AnnotationManager;
use Ocd\PersonnalDataBundle\Service\DataProtectionOfficer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DoctrineSubscriber implements EventSubscriberInterface
{

    private AnnotationManager $annotationManager;
    private DataProtectionOfficer $dataProtectionOfficer;
    private bool $subscribeToDoctrine=false;

    public function __construct(AnnotationManager $annotationManager, DataProtectionOfficer $dataProtectionOfficer, $subscribeToDoctrine=false)
    {
        $this->annotationManager = $annotationManager;
        $this->dataProtectionOfficer = $dataProtectionOfficer;
        $this->subscribeToDoctrine = $subscribeToDoctrine;
    }

    static public function getSubscribedEvents(): array
    {
        return [
            Events::postLoad,
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
        ];
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        if (!$this->subscribeToDoctrine) {
            return ;
        }
        $entity = $args->getObject();
        if($this->annotationManager->hasPersonnalData(ClassUtils::getClass($entity)))
        {
            if($this->dataProtectionOfficer->isProcessContext)
            {
                $this->dataProtectionOfficer->process($entity);
            }
            if($this->dataProtectionOfficer->isRequestContext)
            {
                $this->dataProtectionOfficer->expose($entity);
            }
        }
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        if (!$this->subscribeToDoctrine) {
            return ;
        }
        $entity = $args->getObject();
        if($this->annotationManager->hasPersonnalData(ClassUtils::getClass($entity)))
        {
            $this->dataProtectionOfficer->collect($entity);
        }
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        // dispose ?
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        if (!$this->subscribeToDoctrine) {
            return ;
        }
        $entity = $args->getObject();
        if ($this->annotationManager->hasPersonnalData(ClassUtils::getClass($entity))) 
        {
            /* @var $em Doctrine\ORM\EntityManager */
            $em = $args->getObjectManager();
            $uow = $em->getUnitOfWork();
            $tmpObject = new DoctrineObject($this->entityManager, ClassUtils::getClass($entity));
            $newData = $tmpObject->extract($entity);
            $originalData = $uow->getOriginalEntityData($entity);
            $changes = array_diff_assoc($newData, $originalData);
            $collected = false;
            foreach($changes as $field => $value)
            {
                if($this->annotationManager->isPersonnalData(ClassUtils::getClass($entity), $field))
                {
                    $collected = true;
                }
            }
            if($collected)
            {
                $this->dataProtectionOfficer->collect($entity);
            }
        }
    }
}