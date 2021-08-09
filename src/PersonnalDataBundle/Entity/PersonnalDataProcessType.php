<?php

namespace Ocd\PersonnalDataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Ocd\PersonnalDataBundle\Repository\PersonnalDataProcessTypeRepository;

/**
 * @ORM\Entity(repositoryClass=PersonnalDataProcessTypeRepository::class)
 * @ORM\Table(name="personnal_data_process_type", indexes={
 *     @ORM\Index(name="personnal_data_process_type_constant_code", columns={"constant_code"}),
 *  }, uniqueConstraints={
 *     @ORM\UniqueConstraint(name="personnal_data_process_type_unique_constant_code", columns={"constant_code"})
 *  }
 * )
 * @ORM\HasLifecycleCallbacks
 */
class PersonnalDataProcessType
{
    const IMPORT = 'IMPORT';
    const AUTHENTICATION = 'AUTHENTICATION';
    const LOGIN = 'LOGIN';
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * Unique constant code identifying the process type
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
     * Consents given for this process type
     *
     * @ORM\OneToMany(targetEntity="PersonnalDataConsent", mappedBy="personnalDataProcessType")
     */
    protected $consents;

    /**
     * Consents given for this process type
     *
     * @ORM\OneToMany(targetEntity="PersonnalDataProcess", mappedBy="personnalDataProcessType")
     */
    protected $processes;

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


    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }


    /**
     * Get unique constant code identifying the process type
     */ 
    public function getConstantCode()
    {
        return $this->constantCode;
    }

    /**
     * Set unique constant code identifying the process type
     *
     * @return  self
     */ 
    public function setConstantCode($constantCode)
    {
        $this->constantCode = $constantCode;

        return $this;
    }

    /**
     * Get the process description.
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the process description.
     *
     * @return  self
     */ 
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Gets triggered only on insert
     *
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * Gets triggered every time on update
     *
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get the value of createdAt
     *
     * @return  \DateTime
     */ 
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @param  \DateTime  $createdAt
     *
     * @return  self
     */ 
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of updatedAt
     *
     * @return  \DateTime
     */ 
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @param  \DateTime  $updatedAt
     *
     * @return  self
     */ 
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}