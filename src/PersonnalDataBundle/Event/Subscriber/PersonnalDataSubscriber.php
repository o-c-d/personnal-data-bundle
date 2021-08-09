<?php

namespace Ocd\PersonnalDataBundle\Event\Subscriber;

use Doctrine\Common\Util\ClassUtils;
use Ocd\PersonnalDataBundle\Annotation\AnnotationManager;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataProcess;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataProvider;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataRegister;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataTransport;
use Ocd\PersonnalDataBundle\Event\CollectPersonnalDataEvent;
use Ocd\PersonnalDataBundle\Event\CollectedPersonnalDataEntityEvent;
use Ocd\PersonnalDataBundle\Event\ConsentPersonnalDataEvent;
use Ocd\PersonnalDataBundle\Event\DisposePersonnalDataEvent;
use Ocd\PersonnalDataBundle\Event\ExportPersonnalDataEvent;
use Ocd\PersonnalDataBundle\Event\ExposePersonnalDataEvent;
use Ocd\PersonnalDataBundle\Event\ExposedPersonnalDataEntityEvent;
use Ocd\PersonnalDataBundle\Event\FinalArchivePersonnalDataEvent;
use Ocd\PersonnalDataBundle\Event\IntermediateArchivePersonnalDataEvent;
use Ocd\PersonnalDataBundle\Event\ProcessPersonnalDataEvent;
use Ocd\PersonnalDataBundle\Service\DataProtectionOfficer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PersonnalDataSubscriber implements EventSubscriberInterface
{
    private DataProtectionOfficer $dataProtectionOfficer;

    public function __construct(DataProtectionOfficer $dataProtectionOfficer, AnnotationManager $annotationManager)
    {
        $this->dataProtectionOfficer = $dataProtectionOfficer;
        $this->annotationManager = $annotationManager;
    }

    static public function getSubscribedEvents(): array
    {
        return [
            CollectPersonnalDataEvent::class => 'collectPersonnalData',
            CollectedPersonnalDataEntityEvent::class => 'collectedPersonnalDataEntity',
            ConsentPersonnalDataEvent::class => 'consentPersonnalData',
            ExportPersonnalDataEvent::class => 'exportPersonnalData',
            ExposePersonnalDataEvent::class => 'exposePersonnalData',
            ExposedPersonnalDataEntityEvent::class => 'exposedPersonnalDataEntity',
            DisposePersonnalDataEvent::class => 'disposePersonnalData',
            IntermediateArchivePersonnalDataEvent::class => 'intermediateArchive',
            FinalArchivePersonnalDataEvent::class => 'finalArchive',
            ProcessPersonnalDataEvent::class => 'onProcessPersonnalData',
        ];
    }

    /**
     * Personnal Data collect declared by code
     * Initialize Transport and Provider
     *
     * @param CollectPersonnalDataEvent $event
     * @return void
     */
    public function collectPersonnalData(CollectPersonnalDataEvent $event): void
    {
        /** PersonnalDataTransport $transport */
        $transport = $event->getTransport();
        $this->dataProtectionOfficer->addTransport($transport);
    }

    /**
     * Personnal Data collect declared by Database
     *
     * @param CollectedPersonnalDataEvent $event
     * @return void
     */
    public function collectedPersonnalDataEntity(CollectedPersonnalDataEntityEvent $event): void
    {
        $entity = $event->getEntity();
        if($this->annotationManager->hasPersonnalData(ClassUtils::getClass($entity)))
        {
            $this->dataProtectionOfficer->collect($entity);
        }
        
    }

    /**
     * Someone is giving his consent to use some data for some process
     *
     * @param ConsentPersonnalDataEvent $event
     * @return void
     */
    public function consentPersonnalData(ConsentPersonnalDataEvent $event): void
    {
        /** PersonnalDataProvider $source */
        $source = $event->getSource();
        $process = $event->getProcess();
        $personnalDatas = $event->getPersonnalDatas();
        $this->dataProtectionOfficer->consentEvent($source, $process, $personnalDatas);
    }

    public function exportPersonnalData(ExportPersonnalDataEvent $event): void
    {
        /** PersonnalDataProvider $destination */
        $destination = $event->getDestination();
        /** PersonnalDataTransport $transport */
        $transport = $event->getTransport();
        $personnalDatas = $event->getPersonnalDatas();
        $this->dataProtectionOfficer->exportEvent($destination, $transport, $personnalDatas);
    }

    public function exposePersonnalData(ExposePersonnalDataEvent $event): void
    {
        /** PersonnalDataProvider $destination */
        $destination = $event->getDestination();
        /** PersonnalDataTransport $transport */
        $transport = $event->getTransport();
        $personnalDatas = $event->getPersonnalDatas();
        $this->dataProtectionOfficer->exposeEvent($destination, $transport, $personnalDatas);
    }

    public function exposedPersonnalDataEntity(ExposedPersonnalDataEntityEvent $event): void
    {
        $entity = $event->getEntity();
        $context = $event->getContext();
        if ($this->annotationManager->hasPersonnalData(ClassUtils::getClass($entity))) {
            $this->dataProtectionOfficer->expose($entity, $context);
        }
    }
    

    public function disposePersonnalData(DisposePersonnalDataEvent $event): void
    {
        $personnalDatas = $event->getPersonnalDatas();
        $this->dataProtectionOfficer->disposeEvent($personnalDatas);
    }

    public function intermediateArchive(IntermediateArchivePersonnalDataEvent $event): void
    {
        $personnalDatas = $event->getPersonnalDatas();
        $this->dataProtectionOfficer->intermediateArchiveEvent($personnalDatas);
    }

    public function finalArchive(FinalArchivePersonnalDataEvent $event): void
    {
        $personnalDatas = $event->getPersonnalDatas();
        $this->dataProtectionOfficer->finalArchiveEvent($personnalDatas);
    }

    public function onProcessPersonnalData(ProcessPersonnalDataEvent $event): void
    {
        /** @var PersonnalDataProcess $process*/
        $process = $event->getPersonnalDataProcess();
        $this->dataProtectionOfficer->addProcess($process);
    }
}
