<?php

namespace Ocd\PersonnalDataBundle\Event\Subscriber;

use Ocd\PersonnalDataBundle\Entity\PersonnalDataProcess;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataProcessType;
use Ocd\PersonnalDataBundle\Event\ProcessPersonnalDataEvent;
use Ocd\PersonnalDataBundle\Manager\PersonnalDataProcessManager;
use Ocd\PersonnalDataBundle\Service\DataProtectionOfficer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Event\SwitchUserEvent;
use Symfony\Component\Security\Http\Event\DeauthenticatedEvent;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

class SecuritySubscriber implements EventSubscriberInterface
{
    private DataProtectionOfficer $dataProtectionOfficer;
    private PersonnalDataProcessManager $processManager;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(DataProtectionOfficer $dataProtectionOfficer, PersonnalDataProcessManager $processManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->dataProtectionOfficer = $dataProtectionOfficer;
        $this->processManager = $processManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    static public function getSubscribedEvents(): array
    {
        return [
            AuthenticationSuccessEvent::class => 'authenticationSuccess',
            AuthenticationFailureEvent::class => 'authenticationFailure',
            InteractiveLoginEvent::class => 'authenticationLogin',
            SwitchUserEvent::class => 'authenticationSwitch',
            DeauthenticatedEvent::class => 'authenticationOut',
        ];
    }

    public function authenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $token = $event->getAuthenticationToken();
        if ($token instanceof AnonymousToken) {
            return;
        }
        $user = $token->getUser();
        /** @var PersonnalDataProcess $process */
        $process = $this->processManager->makeProcessByType(PersonnalDataProcessType::AUTHENTICATION, 'User Authentication');
        $this->processManager->addPersonnalDataFromEntity($process, $user);
        $this->eventDispatcher->dispatch(new ProcessPersonnalDataEvent($process));
    }

    public function authenticationFailure(AuthenticationFailureEvent $event): void
    {
    }

    public function authenticationLogin(InteractiveLoginEvent $event): void
    {
        $token = $event->getAuthenticationToken();
        if($token instanceof AnonymousToken)
        {
            return;
        }
        $user = $token->getUser();
        /** @var PersonnalDataProcess $process */
        $process = $this->processManager->makeProcessByType(PersonnalDataProcessType::LOGIN, 'User login Authentication');
        $this->processManager->addPersonnalDataFromEntity($process, $user);
        $this->eventDispatcher->dispatch(new ProcessPersonnalDataEvent($process));
    }

    public function authenticationSwitch(SwitchUserEvent $event): void
    {
    }

    public function authenticationOut(DeauthenticatedEvent $event): void
    {
    }

}