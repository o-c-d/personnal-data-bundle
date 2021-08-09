<?php

namespace Ocd\PersonnalDataBundle\DataCollector;

use Ocd\PersonnalDataBundle\Service\DataProtectionOfficer;
use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PersonnalDataCollector extends AbstractDataCollector
{

    private $dataProtectionOfficer;

    public function __construct(DataProtectionOfficer $dpo)
    {
        $this->dpo = $dpo;
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null)    {
        $this->data = [
            'transports' => $this->dpo->getTransports(),
            'processes' => $this->dpo->getProcesses(),
            'collected' => $this->dpo->getCollected(),
            'exposed' => $this->dpo->getExposed(),
        ];
    }

    public function getStatusColor()
    {
        $countEvent = self::countPersonnalDataFromEvent($this->data['transports'])+self::countPersonnalDataFromEvent($this->data['processes']);
        $countDb = self::countPersonnalDataFromDb($this->data['collected'])+self::countPersonnalDataFromDb($this->data['exposed']);

        if ($countEvent < $countDb) {
            return '#db2e2e';   // red
        } elseif ($countEvent > $countDb) {
            return '#A46A1F';   // yellow
        } else {
            return '#4F805D';     //green
        }
    }

    static public function getPersonnalDataRegitersFromEvent(array $collection=[]): array
    {
        $personnalDatas = [];
        foreach ($collection as $index => $object) {
            $objectPersonnalDatas = $object->getPersonnalDatas();
            foreach ($objectPersonnalDatas as $index => $objectPersonnalData) {
                $personnalDatas[] = $objectPersonnalData;

            }
        }
        return $personnalDatas;
    }

    static public function getPersonnalDataFromEvent(array $collection=[]): array
    {
        $personnalDatas = [];
        foreach ($collection as $index => $object) {
            $objectPersonnalDatas = $object->getPersonnalDatas();
            foreach ($objectPersonnalDatas as $index => $objectPersonnalData) {
                if (!isset($personnalDatas[$objectPersonnalData->getEntityName()])) {
                    $personnalDatas[$objectPersonnalData->getEntityName()] = [];
                }
                if (!isset($personnalDatas[$objectPersonnalData->getEntityName()][$objectPersonnalData->getEntityId()])) {
                    $personnalDatas[$objectPersonnalData->getEntityName()][$objectPersonnalData->getEntityId()] = [];
                }
                if (!isset($personnalDatas[$objectPersonnalData->getEntityName()][$objectPersonnalData->getEntityId()][$objectPersonnalData->getFieldName()])) {
                    $personnalDatas[$objectPersonnalData->getEntityName()][$objectPersonnalData->getEntityId()][$objectPersonnalData->getFieldName()] = [];
                }
            }
        }
        return $personnalDatas;
    }

    static public function countPersonnalDataFromEvent(array $collection=[]): int
    {
        $personnalDatas = self::getPersonnalDataFromEvent($collection);
        $count = 0;
        foreach ($personnalDatas as $entityName => $entity)
        {
            foreach($entity as $entityId => $fields)
            {
                $count += count($fields);
            }
        }
        return $count;
    }

    static public function countPersonnalDataFromDb($collection=[]): int
    {
        $count = 0;
        foreach($collection as $entityName => $entity)
        {
            foreach ($entity as $entityId => $fields) {
                $count += count($fields);
            }
        }
        return $count;
    }


    public function getTransports()
    {
        return $this->data['transports'];
    }

    public function getProcesses()
    {
        return $this->data['processes'];
    }

    public function getCollected()
    {
        return $this->data['collected'];
    }

    public function getExposed()
    {
        return $this->data['exposed'];
    }

    public function getErrors()
    {
        return $this->data['errors'];
    }

    public function getViolations()
    {
        return $this->data['violations'];
    }

}