<?php

namespace Ocd\PersonnalDataBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataProvider;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataRegister;
use Ocd\PersonnalDataBundle\Entity\PersonnalDataTransport;
use Symfony\Component\HttpFoundation\Request;

class PersonnalDataTransportManager
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function makeTransport(string $transportType, Request $request, PersonnalDataProvider $provider, array $personnalDatas=[]): PersonnalDataTransport
    {
        $transport = new PersonnalDataTransport();
        $transport->setType($transportType);
        $transport->setProtocol($request->getProtocol());
        $transport->setRoute($request->get('_route'));
        if(PersonnalDataTransport::TYPE_COLLECT == $transportType)
        {
            $transport->setFromIp($request->getClientIp());
            $transport->setToIp($request->server->get('SERVER_ADDR'));
        } else {
            $transport->setFromIp($request->$request->server->get('SERVER_ADDR'));
            $transport->setToIp($request->getClientIp());
        }
        $transport->setProvider($provider);
        foreach($personnalDatas as $personnalData)
        {
            if($personnalData instanceof PersonnalDataRegister)
            {
                $transport->addPersonnalData($personnalData);
            }
        }
        $this->em->persist($transport);
        return $transport;
    }

}