<?php

namespace Ocd\PersonnalDataBundle\Event\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\OnClearEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Ocd\PersonnalDataBundle\Annotation\AnnotationManager;
use Ocd\PersonnalDataBundle\Event\CollectedPersonnalDataEntityEvent;
use Ocd\PersonnalDataBundle\Event\ExposedPersonnalDataEntityEvent;
use Ocd\PersonnalDataBundle\Service\DataProtectionOfficer;
use Ocd\PersonnalDataBundle\Service\PersonnalDataCollector;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DoctrineSubscriber implements EventSubscriber
{

    private EventDispatcherInterface $eventDispatcher;
    private string $kernelEnv;
    private bool $subscribeToDoctrine=false;
    private bool $debugBacktrace=false;

    public function __construct(EventDispatcherInterface $eventDispatcher, $kernelEnv, $subscribeToDoctrine=false, $debugBacktrace=false)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->kernelEnv = $kernelEnv;
        $this->subscribeToDoctrine = $subscribeToDoctrine;
        $this->debugBacktrace = $debugBacktrace;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postLoad,
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
            Events::onClear,
        ];
    }

    public function onClear(OnClearEventArgs $args): void
    {
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        if (!$this->subscribeToDoctrine) {
            return ;
        }
        $entity = $args->getObject();
        $context = [];
        if('dev'===$this->kernelEnv && $this->debugBacktrace)
        {
            $context = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        }
        $this->eventDispatcher->dispatch(new ExposedPersonnalDataEntityEvent($entity, $context));
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        if (!$this->subscribeToDoctrine) {
            return ;
        }
        $entity = $args->getObject();
        $context = [];
        if('dev'===$this->kernelEnv && $this->debugBacktrace)
        {
            $context = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        }
        $this->eventDispatcher->dispatch(new CollectedPersonnalDataEntityEvent($entity, $context));
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
        $context = [];
        if('dev'===$this->kernelEnv && $this->debugBacktrace)
        {
            $context = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        }
        $this->eventDispatcher->dispatch(new CollectedPersonnalDataEntityEvent($entity, $context));

        // if ($this->annotationManager->hasPersonnalData(ClassUtils::getClass($entity))) 
        // {
        //     /* @var $em Doctrine\ORM\EntityManager */
        //     $em = $args->getObjectManager();
        //     $uow = $em->getUnitOfWork();
        //     $tmpObject = new DoctrineObject($this->entityManager, ClassUtils::getClass($entity));
        //     $newData = $tmpObject->extract($entity);
        //     $originalData = $uow->getOriginalEntityData($entity);
        //     $changes = array_diff_assoc($newData, $originalData);
        //     $collected = false;
        //     foreach($changes as $field => $value)
        //     {
        //         if($this->annotationManager->isPersonnalData(ClassUtils::getClass($entity), $field))
        //         {
        //             $collected = true;
        //         }
        //     }
        //     if($collected)
        //     {
        //         $this->dataProtectionOfficer->collect($entity);
        //     }
        // }
    }
}