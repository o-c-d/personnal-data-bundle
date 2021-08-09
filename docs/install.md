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
use Ocd\PersonnalDataBundle\Annotation\PersonnalData;
use Ocd\PersonnalDataBundle\Annotation\PersonnalDataReceipt;

/**
 * @ORM\Entity(name="user")
 * @PersonnalDataReceipt(name="User", description="User table", cascadeTo={}, isPersonnalDataProvider=true)
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
use Ocd\PersonnalDataBundle\Manager\PersonnalDataResgisterManager;
 

// ...
$transport = $this->PersonnalDataOfficer->getTransportByType(PersonnalDataTransport::TYPE_COLLECT);
$transport->setDescription('Registration form.');
$source = $this->PersonnalDataProviderManager->getProviderByEntity($user);
// If $entity is the collected entity, get personnal data from it, else just declare $personnalDatas as empty array.
if($entity) $personnalDatas = $this->PersonnalDataRegisterManager->makeAllPersonnalDataRegistersFromEntity($entity);
else $personnalDatas = [];
$event = new CollectPersonnalDataEvent($transport, $source, $personnalDatas);
$dispatcher->dispatch($event, CollectPersonnalDataEvent::NAME);
```

 5/ initialize database and PersonnalDataRegister
If you install this bundle on an existing project, you may have some personnal data stored but not referenced in PersonnalDataRegister tables. There is a command to retrieve all your personnal datas in your database, according to your annotations declarations, and declare them in PersonnalDataRegister table. You can also create a consent for them.
> php bin/console ocd:personnal-data:initialize

 6/ Experimental features : subscribe to doctrine events
When dispatching collect event, it may be difficult to have the collected entity and declare the PersonnalDataRegister.
If you enable subscribe_to_doctrine option, all inserts will add collected PersonnalDataRegister to the current PersonnalDataTransport declared with event CollectPersonnalDataEvent.

 7/ Experimental features : doctrine declare transports
If you enable doctrine_declare_transports option, all inserts will add collected PersonnalDataRegister to the current PersonnalDataTransport declared with event CollectPersonnalDataEvent or create a new one if needed (Only one PersonnalDataTransport of each type may exists simultaneously).
