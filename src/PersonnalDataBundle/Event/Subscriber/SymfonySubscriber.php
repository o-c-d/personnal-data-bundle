<?php

namespace Ocd\PersonnalDataBundle\Event\Subscriber;

use Ocd\PersonnalDataBundle\Service\DataProtectionOfficer;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataProcessType;
use Ocd\PersonnalDataBundle\Event\ProcessPersonnalDataEvent;
use Ocd\PersonnalDataBundle\Manager\PersonnalDataProcessManager;
use Symfony\Component\Security\Core\User\UserInterface;

class SymfonySubscriber implements EventSubscriberInterface
{
    private DataProtectionOfficer $dataProtectionOfficer;
    private PersonnalDataProcessManager $processManager;
    private EventDispatcherInterface $eventDispatcher;
    private Security $security;

    public function __construct(DataProtectionOfficer $dataProtectionOfficer, PersonnalDataProcessManager $processManager, EventDispatcherInterface $eventDispatcher, Security $security)
    {
        $this->dataProtectionOfficer = $dataProtectionOfficer;
        $this->processManager = $processManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->security = $security;
    }

    static public function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'kernelRequested',
            KernelEvents::VIEW => 'kernelView',
            KernelEvents::RESPONSE => 'kernelResponse',
            KernelEvents::FINISH_REQUEST => 'kernelFinishRequest',
            KernelEvents::TERMINATE => 'kernelTerminate',
            ConsoleEvents::COMMAND => 'consoleLoaded',
            ConsoleEvents::TERMINATE => 'consoleTerminated',
        ];
    }

    public function kernelRequested(RequestEvent $event): void
    {
        $this->dataProtectionOfficer->isRequestContext = true;
    }

    public function kernelView(ViewEvent $event): void
    {
    }

    public function kernelResponse(ResponseEvent $event): void
    {
        $auth = false;
        $processes = $this->dataProtectionOfficer->getProcesses();
        foreach($processes as $process)
        {
            if(PersonnalDataProcessType::AUTHENTICATION === $process->getPersonnalDataProcessType()->getConstantCode())
            {
                $auth = true;
            }
        }
        if(!$auth)
        {
            /** @var ?UserInterface $user */
            $user = $this->security->getUser();
            if ($user instanceof UserInterface) {
                /** @var PersonnalDataProcess $process */
                $process = $this->processManager->makeProcessByType(PersonnalDataProcessType::AUTHENTICATION, 'User Authentication');
                $this->processManager->addPersonnalDataFromEntity($process, $user);
                $this->eventDispatcher->dispatch(new ProcessPersonnalDataEvent($process));
            }
        }
    }

    public function kernelFinishRequest(FinishRequestEvent $event): void
    {
    }

    public function kernelTerminate(TerminateEvent $event): void
    {
        $this->dataProtectionOfficer->validation();
    }

    public function consoleLoaded(ConsoleCommandEvent $event): void
    {
        $this->dataProtectionOfficer->isConsoleContext = true;
    }

    public function consoleTerminated(ConsoleTerminateEvent $event): void
    {
        $this->dataProtectionOfficer->validation();
    }
}
