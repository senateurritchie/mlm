<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PercentagePerMembreType
 *
 * @ORM\Table(name="percentage_per_membre_type", options={"comment":"pourcentage généré par un membre de mon reseau"})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PercentagePerMembreTypeRepository")
 */
class PercentagePerMembreType
{
    /**
    * @var int
    *
    * @ORM\Column(name="id", type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
    * @var AppBundle\Entity\MembreType
    *
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MembreType")
    * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
    */
    private $parrainType;

    /**
    * @var AppBundle\Entity\MembreType
    *
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MembreType")
    * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
    */
    private $childType;

    /**
    * @var float
    *
    * @ORM\Column(name="value", type="float")
    */
    private $value;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set value
     *
     * @param float $value
     *
     * @return PercentagePerMembreType
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set parrainType
     *
     * @param \AppBundle\Entity\MembreType $parrainType
     *
     * @return PercentagePerMembreType
     */
    public function setParrainType(\AppBundle\Entity\MembreType $parrainType = null)
    {
        $this->parrainType = $parrainType;

        return $this;
    }

    /**
     * Get parrainType
     *
     * @return \AppBundle\Entity\MembreType
     */
    public function getParrainType()
    {
        return $this->parrainType;
    }

    /**
     * Set childType
     *
     * @param \AppBundle\Entity\MembreType $childType
     *
     * @return PercentagePerMembreType
     */
    public function setChildType(\AppBundle\Entity\MembreType $childType = null)
    {
        $this->childType = $childType;

        return $this;
    }

    /**
     * Get childType
     *
     * @return \AppBundle\Entity\MembreType
     */
    public function getChildType()
    {
        return $this->childType;
    }
}
