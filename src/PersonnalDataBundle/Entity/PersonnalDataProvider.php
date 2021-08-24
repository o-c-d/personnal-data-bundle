<?php

namespace Ocd\PersonnalDataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Ocd\PersonnalDataBundle\Repository\PersonnalDataProviderRepository;

/**
 * @ORM\Entity(repositoryClass=PersonnalDataProviderRepository::class)
 * @ORM\Table(name="personnal_data_provider",
 * indexes={
 *     @ORM\Index(name="personnal_data_provider_entity_name_entity_id", columns={"entity_name", "entity_id"}),
 *  }, uniqueConstraints={
 *     @ORM\UniqueConstraint(name="personnal_data_provider_unique_provider_entity", columns={"entity_name", "entity_id"})
 *  }
 * )
 * @ORM\HasLifecycleCallbacks
 */
class PersonnalDataProvider
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;
    
    /**
     * The provider description.
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    protected ?string $description;
    
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
     * Transports to this provider
     *
     * @ORM\OneToMany(targetEntity="PersonnalDataTransport", mappedBy="personnalDataProvider")
     */
    protected $transports;

    /**
     * Consents given by this provider
     *
     * @ORM\OneToMany(targetEntity="PersonnalDataConsent", mappedBy="personnalDataProvider")
     */
    protected $consents;

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
     * Get the provider description.
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the provider description.
     *
     * @return  self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
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
