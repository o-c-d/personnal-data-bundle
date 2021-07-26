<?php

namespace Ocd\PersonnalDataBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * Adds intermediateArchivedAt and finalArchivedAt timestamps to entities.
 */
trait Archivable
{
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
     * Get the value of intermediateArchivedAt
     *
     * @return  ?DateTime
     */ 
    public function getIntermediateArchivedAt()
    {
        return $this->intermediateArchivedAt;
    }

    /**
     * Set the value of intermediateArchivedAt
     *
     * @param  ?DateTime  $intermediateArchivedAt
     *
     * @return  self
     */ 
    public function setIntermediateArchivedAt(?DateTime $intermediateArchivedAt)
    {
        $this->intermediateArchivedAt = $intermediateArchivedAt;

        return $this;
    }

    /**
     * Get the value of finalArchivedAt
     *
     * @return  ?DateTime
     */ 
    public function getFinalArchivedAt()
    {
        return $this->finalArchivedAt;
    }

    /**
     * Set the value of finalArchivedAt
     *
     * @param  ?DateTime  $finalArchivedAt
     *
     * @return  self
     */ 
    public function setFinalArchivedAt(?DateTime $finalArchivedAt)
    {
        $this->finalArchivedAt = $finalArchivedAt;

        return $this;
    }
}
