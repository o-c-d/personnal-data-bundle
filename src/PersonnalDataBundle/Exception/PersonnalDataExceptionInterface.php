<?php

namespace Ocd\PersonnalDataBundle\Exception;

use Ocd\PersonnalDataBundle\Entity\PersonnalDataRegister;

interface PersonnalDataExceptionInterface extends \Throwable
{
    public function getPersonnalData(): PersonnalDataRegister;
}