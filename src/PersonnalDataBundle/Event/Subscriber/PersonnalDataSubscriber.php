<?php

namespace Ocd\PersonnalDataBundle\Event\Subscriber;

use Ocd\PersonnalDataBundle\Entity\PersonnalDataProcess;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataProvider;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataRegister;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataTransport;
use Ocd\PersonnalDataBundle\Event\CollectPersonnalDataEvent;
use Ocd\PersonnalDataBundle\Event\ConsentPersonnalDataEvent;
use Ocd\PersonnalDataBundle\Event\DisposePersonnalDataEvent;
use Ocd\PersonnalDataBundle\Event\ExportPersonnalDataEvent;
use Ocd\PersonnalDataBundle\Event\ExposePersonnalDataEvent;
use Ocd\PersonnalDataBundle\Event\FinalArchivePersonnalDataEvent;
use Ocd\PersonnalDataBundle\Event\IntermediateArchivePersonnalDataEvent;
use Ocd\PersonnalDataBundle\Service\DataProtectionOfficer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PersonnalDataSubscriber implements EventSubscriberInterface
{
    private DataProtectionOfficer $dataProtectionOfficer;

    public function __construct(DataProtectionOfficer $dataProtectionOfficer)
    {
        $this->dataProtectionOfficer = $dataProtectionOfficer;
        $this->annotationManager = $annotationManager;
    }

    static public function getSubscribedEvents(): array
    {
        return [
            CollectPersonnalDataEvent::class => 'collectPersonnalData',
            ConsentPersonnalDataEvent::class => 'consentPersonnalData',
            ExportPersonnalDataEvent::class => 'exportPersonnalData',
            ExposePersonnalDataEvent::class => 'exposePersonnalData',
            DisposePersonnalDataEvent::class => 'disposePersonnalDataEvent',
            IntermediateArchivePersonnalDataEvent::class => 'intermediateArchive',
            FinalArchivePersonnalDataEvent::class => 'finalArchive',
        ];
    }

    /**
     * Collecting Personnal Data in progress
     * Initialize Transport and Provider
     *
     * @param CollectPersonnalDataEvent $event
     * @return void
     */
    public function collectPersonnalData(CollectPersonnalDataEvent $event): void
    {
        /** PersonnalDataTransport $transport */
        $transport = $event->getTransport();
        /** PersonnalDataProvider $source */
        $source = $event->getSource();
        /** PersonnalDataRegister[] $personnalDatas */
        $personnalDatas = $event->getPersonnalDatas();
        $this->dataProtectionOfficer->collectEvent($transport, $source, $personnalDatas);
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

    public function disposePersonnalDataEvent(DisposePersonnalDataEvent $event): void
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
}
