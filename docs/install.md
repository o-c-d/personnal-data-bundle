# Personnal Data Bundle Installation

1/ Install the Bundle via Composer
>  composer require o-c-d/personnal-data-bundle

 2/ Configurations
 Two experimentals features can be enabled via configuration file but should not be enabled until installation is done.

 3/ Declare annotations
Tables containing Personnal Datas should have a PersonnalDataReceipt annotation.
Fields containing Personnal Datas should have a PersonnalData annotation.

Example in User Table :
```
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(name="user")
 * @PersonnalDataReceipt(name="User", description="User table", cascadeTo={})
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @PersonnalData(name="login", description="Email Address used for login user")
     */
    private $email;

    // ...
}
```

 4/ Dispatch events
You have to dispatch events yourself where your application collects or exposes personnal datas.

Example for registration form:
```
use Ocd\PersonnalDataBundle\Event\CollectPersonnalDataEvent;
use Ocd\PersonnalDataBundle\Service\PersonnalDataOfficer;
use Ocd\PersonnalDataBundle\Manager\PersonnalDataProviderManager;

// ...
$transport = $this->personnalDataOfficer->getTransportByType(PersonnalDataTransport::TYPE_COLLECT);
$source = $this->PersonnalDataProviderManager->getProviderByEntity($user);
$event = new CollectPersonnalDataEvent($transport, $source, $personnalDatas);
$dispatcher->dispatch($event, CollectPersonnalDataEvent::NAME);
```
 5/ initialize database and PersonnalDataRegister

 6/ Experimental features : subscribe to doctrine events

 7/ Experimental features : doctrine declare transports
