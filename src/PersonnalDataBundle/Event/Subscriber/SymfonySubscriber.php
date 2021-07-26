<?php

namespace Ocd\PersonnalDataBundle\Event\Subscriber;

use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;


class SymfonySubscriber implements EventSubscriberInterface
{
    private DataProtectionOfficer $dataProtectionOfficer;

    public function __construct(DataProtectionOfficer $dataProtectionOfficer)
    {
        $this->dataProtectionOfficer = $dataProtectionOfficer;
    }

    public function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'kernelRequested',
            KernelEvents::TERMINATE => 'kernelTerminated',
            ConsoleEvents::COMMAND => 'consoleLoaded',
            ConsoleEvents::TERMINATE => 'consoleTerminated',
        ];
    }

    public function kernelRequested(RequestEvent $event): void
    {
        $this->dataProtectionOfficer->isRequestContext = true;
    }

    public function kernelTerminated(TerminateEvent $event): void
    {
        $this->dataProtectionOfficer->persistTransports();
    }

    public function consoleLoaded(ConsoleCommandEvent $event): void
    {
        $this->dataProtectionOfficer->isConsoleContext = true;
    }

    public function consoleTerminated(ConsoleTerminateEvent $event): void
    {
        $this->dataProtectionOfficer->persistTransports();
    }
}
