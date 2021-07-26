<?php

namespace Ocd\PersonnalDataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Ocd\PersonnalDataBundle\Repository\PersonnalDataTransportRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PersonnalDataTransportRepository::class)
 * @ORM\Table(name="personnal_data_transport")
 * @ORM\HasLifecycleCallbacks
 */
class PersonnalDataTransport
{
    const TYPE_COLLECT = "COLLECT";
    const TYPE_EXPOSE = "EXPOSE";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected ?int $id;

    /**
     * Transport name
     *
     * @ORM\Column(name="name", type="string", nullable=true)
     * @var ?string
     */
    protected ?string $name;

    /**
     * Transport description
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     * @var ?string
     */
    protected ?string $description;

    /**
     * Transport type (collect/expose/export/...)
     *
     * @ORM\Column(name="type", type="string", nullable=true)
     * @var ?string
     */
    protected ?string $type;

    /**
     * Data format used during transport (JSON/HTML/PDF...)
     *
     * @ORM\Column(name="format", type="string", nullable=true)
     * @var ?string
     */
    protected ?string $format;

    /**
     * The protocol used for transport (HTTPS/SFTP/...)
     *
     * @ORM\Column(name="protocol", type="string", nullable=true)
     * @var ?string
     */
    protected ?string $protocol;

    /**
     * IP Address delivering the data
     *
     * @ORM\Column(name="from_ip", type="string", nullable=true)
     * @Assert\Ip()
     * @var ?string
     */
    protected ?string $fromIp;

    /**
     * IP Address receiving the data
     *
     * @ORM\Column(name="to_ip", type="string", nullable=true)
     * @var ?string
     */
    protected ?string $toIp;

    /**
     * List of personnaldata transported
     *
     * @ORM\ManyToMany(targetEntity="PersonnalDataRegister", inversedBy="transports")
     * @ORM\JoinTable(name="personnal_data_transports_registers")
     * 
     * @var PersonnalDataRegister[]
     */
    protected $personnalDatas;

    /**
     * personnalDataProvider responsible for transport
     *
     * @ManyToOne(targetEntity="PersonnalDataProvider", inversedBy="transports")
     * @JoinColumn(name="personnal_data_provider_id", referencedColumnName="id")
     * @var PersonnalDataProvider
     */
    protected $personnalDataProvider;

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
     * Get the value of id
     *
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get transport type (collect/expose/export/...)
     *
     * @return  ?string
     */ 
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set transport type (collect/expose/export/...)
     *
     * @param  ?string  $type  Transport type (collect/expose/export/...)
     *
     * @return  self
     */ 
    public function setType(?string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get data format used during transport (JSON/HTML/PDF...)
     *
     * @return  ?string
     */ 
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set data format used during transport (JSON/HTML/PDF...)
     *
     * @param  ?string  $format  Data format used during transport (JSON/HTML/PDF...)
     *
     * @return  self
     */ 
    public function setFormat(?string $format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get the protocol used for transport (HTTPS/SFTP/...)
     *
     * @return  ?string
     */ 
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Set the protocol used for transport (HTTPS/SFTP/...)
     *
     * @param  ?string  $protocol  The protocol used for transport (HTTPS/SFTP/...)
     *
     * @return  self
     */ 
    public function setProtocol(?string $protocol)
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * Get iP Address delivering the data
     *
     * @return  ?string
     */ 
    public function getFromIp()
    {
        return $this->fromIp;
    }

    /**
     * Set iP Address delivering the data
     *
     * @param  ?string  $fromIp  IP Address delivering the data
     *
     * @return  self
     */ 
    public function setFromIp(?string $fromIp)
    {
        $this->fromIp = $fromIp;

        return $this;
    }

    /**
     * Get iP Address receiving the data
     *
     * @return  ?string
     */ 
    public function getToIp()
    {
        return $this->toIp;
    }

    /**
     * Set iP Address receiving the data
     *
     * @param  ?string  $toIp  IP Address receiving the data
     *
     * @return  self
     */ 
    public function setToIp(?string $toIp)
    {
        $this->toIp = $toIp;

        return $this;
    }

    /**
     * Get list of personnaldata transported
     *
     * @return  PersonnalDataRegister[]
     */ 
    public function getPersonnalDatas()
    {
        return $this->personnalDatas;
    }

    /**
     * Set list of personnaldata transported
     *
     * @param  PersonnalDataRegister[]  $personnalDatas  List of personnaldata transported
     *
     * @return  self
     */ 
    public function setPersonnalDatas(array $personnalDatas): self
    {
        $this->personnalDatas = $personnalDatas;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param PersonnalDataRegister $personnalData
     * @return void
     */
    public function addPersonnalData(PersonnalDataRegister $personnalData): self
    {
        if ($this->personnalDatas->contains($personnalData)) {
            return $this;
        }
        $this->personnalDatas[] = $personnalData;

        return $this;
    }

    /**
     * Remove a Personnal Data from transport
     *
     * @param PersonnalDataRegister $personnalData
     * @return self
     */
    public function removePersonnalData(PersonnalDataRegister $personnalData): self
    {
        $this->personnalDatas->removeElement($personnalData);

        return $this;
    }

    /**
     * Get personnalDataProvider responsible for transport
     *
     * @return  PersonnalDataProvider
     */ 
    public function getPersonnalDataProvider()
    {
        return $this->personnalDataProvider;
    }

    /**
     * Set personnalDataProvider responsible for transport
     *
     * @param  PersonnalDataProvider  $personnalDataProvider  personnalDataProvider responsible for transport
     *
     * @return  self
     */ 
    public function setPersonnalDataProvider(PersonnalDataProvider $personnalDataProvider)
    {
        $this->personnalDataProvider = $personnalDataProvider;

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