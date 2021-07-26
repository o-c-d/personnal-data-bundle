<?php

namespace Ocd\PersonnalDataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Ocd\PersonnalDataBundle\Repository\PersonnalDataProcessRepository;


/**
 * @ORM\Entity(repositoryClass=PersonnalDataProcessRepository::class)
 * @ORM\Table(name="personnal_data_transport")
 */
class PersonnalDataProcess
{

    const IMPORT = 'IMPORT';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * Unique constant code identifying the process
     * @ORM\Column(name="constant_code", type="string")
     */
    protected string $constantCode;
    
    /**
     * The process description.
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    protected ?string $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

}
