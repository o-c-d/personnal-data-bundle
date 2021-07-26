<?php

namespace Ocd\PersonnalDataBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Ocd\PersonnalDataBundle\Repository\PersonnalDataRegisterRepository;

/**
 * @ORM\Entity(repositoryClass=PersonnalDataRegisterRepository::class)
 * @ORM\Table(name="personnal_data_register")
 * @ORM\HasLifecycleCallbacks
 */
class PersonnalDataRegister
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;
    
    /**
     * The entity class name.
     *
     * @ORM\Column(name="entity_name", type="string")
     */
    protected string $entityName;

    /**
     * The entity ID.
     *
     * @ORM\Column(name="entity_id", type="string")
     */
    protected string $entityId;

    /**
     * The entity field name.
     *
     * @ORM\Column(name="field_name", type="string")
     */
    protected string $fieldName;

    /**
     * List of transports for this personnal data
     *
     * @ORM\OneToMany(targetEntity="PersonnalDataTransport", mappedBy="personnalDatas")
     * @var PersonnalDataTransport[]
     */
    protected $transports;

    /**
     * Consents given for this personnal data
     *
     * @ORM\OneToMany(targetEntity="PersonnalDataConsent", mappedBy="personnalDatas")
     * @var PersonnalDataConsent[]
     */
    protected $consents;

    /**
     * @var ?DateTime
     *
     * @ORM\Column(name="intermediate_archived_at", type="datetime", nullable=true)
     */
    private $intermediateArchivedAt;

    /**
     * @var ?DateTime
     *
     * @ORM\Column(name="final_archived_at", type="datetime", nullable=true)
     */
    private $finalArchivedAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var ?DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

     public function __construct()
     {
         $this->transports = new ArrayCollection();
     }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the entity class name.
     */ 
    public function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * Set the entity class name.
     *
     * @return  self
     */ 
    public function setEntityName($entityName)
    {
        $this->entityName = $entityName;

        return $this;
    }

    /**
     * Get the entity ID.
     */ 
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Set the entity ID.
     *
     * @return  self
     */ 
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * Get the entity field name.
     */ 
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * Set the entity field name.
     *
     * @return  self
     */ 
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;

        return $this;
    }

    /**
     * Get list of transports for this personnal data
     *
     * @return  PersonnalDataTransport[]
     */ 
    public function getTransports()
    {
        return $this->transports;
    }

    /**
     * Add a PersonnalDataTransport to PersonnalDataRegister
     *
     * @param  PersonnalDataTransport[]  $transports  List of transports for this personnal data
     *
     * @return  self
     */ 
    public function addTransport(PersonnalDataTransport $transport)
    {
        if ($this->transports->contains($transport)) {
            return $this;
        }
        $this->transports[] = $transport;

        return $this;
    }

    /**
     * Remove a PersonnalDataTransport from PersonnalDataRegister
     *
     * @param PersonnalDataRegister $personnalData
     * @return self
     */
    public function removeTransport(PersonnalDataTransport $transport): self
    {
        $this->transports->removeElement($transport);

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

