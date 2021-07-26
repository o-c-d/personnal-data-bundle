<?php

namespace Ocd\PersonnalDataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Ocd\PersonnalDataBundle\Repository\PersonnalDataConsentRepository;

/**
 * @ORM\Entity(repositoryClass=PersonnalDataConsentRepository::class)
 * @ORM\Table(name="personnal_data_transport")
 */
class PersonnalDataConsent
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * personnalDataProvider giving consent
     *
     * @ORM\ManyToOne(targetEntity="PersonnalDataProvider", inversedBy="transports")
     * @ORM\JoinColumn(name="personnal_data_provider_id", referencedColumnName="id")
     * @var PersonnalDataProvider
     */
    protected $personnalDataProvider;

    /**
     * PersonnalDataProcess concerned by consent
     *
     * @ORM\ManyToMany(targetEntity="PersonnalDataProcess", inversedBy="consents")
     * @ORM\JoinColumn(name="personnal_data_process_id", referencedColumnName="id")
     * @var PersonnalDataProcess
     */
    protected $personnalDataProcess;

    /**
     * List of personnaldata concerned by consent
     *
     * @ORM\ManyToMany(targetEntity="PersonnalDataRegister", inversedBy="consents")
     * @ORM\JoinTable(name="personnal_data_consents_registers")
     * 
     * @var PersonnalDataRegister[]
     */
    protected $personnalDatas = [];

    /**
     * The consent has been revoked
     * 
     * @ORM\Column(name="is_revoked", type="boolean", options={"default": false})
     */
    protected bool $isRevoked = false;


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