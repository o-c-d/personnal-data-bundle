<?php

namespace Ocd\PersonnalDataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Ocd\PersonnalDataBundle\Repository\PersonnalDataProcessRepository;


/**
 * @ORM\Entity(repositoryClass=PersonnalDataProcessRepository::class)
 * @ORM\Table(name="personnal_data_process", indexes={
 *     @ORM\Index(name="personnal_data_process_personnal_data_process_type", columns={"personnal_data_process_type_id"}),
 *  }
 * )
 * @ORM\HasLifecycleCallbacks
 */
class PersonnalDataProcess
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * PersonnalDataProcessType 
     *
     * @ORM\ManyToOne(targetEntity="PersonnalDataProcessType", inversedBy="processes")
     * @ORM\JoinColumn(name="personnal_data_process_type_id", referencedColumnName="id")
     * @var PersonnalDataProcessType
     */
    protected $personnalDataProcessType;
    
    /**
     * The process comment.
     *
     * @ORM\Column(name="comment", type="string", nullable=true)
     */
    protected ?string $comment;

    /**
     * List of personnaldata transported
     *
     * @ORM\ManyToMany(targetEntity="PersonnalDataRegister", inversedBy="processes")
     * @ORM\JoinTable(name="personnal_data_processes_registers")
     * 
     * @var PersonnalDataRegister[]
     */
    protected $personnalDatas;

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
        $this->personnalDatas = new ArrayCollection();
    }

    /**
     * Get personnalDataProcessType
     *
     * @return  PersonnalDataProcessType
     */ 
    public function getPersonnalDataProcessType()
    {
        return $this->personnalDataProcessType;
    }

    /**
     * Set personnalDataProcessType
     *
     * @param  PersonnalDataProcessType  $personnalDataProcessType  PersonnalDataProcessType
     *
     * @return  self
     */ 
    public function setPersonnalDataProcessType(PersonnalDataProcessType $personnalDataProcessType)
    {
        $this->personnalDataProcessType = $personnalDataProcessType;

        return $this;
    }

    /**
     * Get the process comment.
     */ 
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set the process comment.
     *
     * @return  self
     */ 
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    public function addPersonnalDataRegister(PersonnalDataRegister $personnalDataRegister): self
    {
        if (!$this->personnalDatas->contains($personnalDataRegister)) {
            $this->personnalDatas[] = $personnalDataRegister;
        }

        return $this;
    }

    public function getPersonnalDatas()
    {
        return $this->personnalDatas;
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
