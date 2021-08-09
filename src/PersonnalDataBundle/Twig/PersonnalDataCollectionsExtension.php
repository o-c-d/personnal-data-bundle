<?php

namespace Ocd\PersonnalDataBundle\Twig;

use Ocd\PersonnalDataBundle\DataCollector\PersonnalDataCollector;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigFilter;

class PersonnalDataCollectionsExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('countPersonnalDataFromEvent', [$this, 'countPersonnalDataFromEvent']),
            new TwigFunction('countPersonnalDataFromDb', [$this, 'countPersonnalDataFromDb']),
        ];
    }
    public function getFilters()
    {
        return [
            new TwigFilter('countPersonnalDataFromEvent', [$this, 'countPersonnalDataFromEvent']),
            new TwigFilter('countPersonnalDataFromDb', [$this, 'countPersonnalDataFromDb']),
        ];
    }


    static public function countPersonnalDataFromEvent($collection=[]): int
    {
        $count = 0;
        foreach ($collection as $index => $object) {
            $count += count($object->getPersonnalDatas());
        }
        return PersonnalDataCollector::countPersonnalDataFromEvent($collection);
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
        return PersonnalDataCollector::countPersonnalDataFromDb($collection);
        return $count;
    }
}
