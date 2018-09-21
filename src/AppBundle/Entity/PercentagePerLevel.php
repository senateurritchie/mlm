<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PercentagePerLevel
 *
 * @ORM\Table(name="percentage_per_level", options={"comment":"les bonus de pourcentages par generation"})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PercentagePerLevelRepository")
 */
class PercentagePerLevel
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
    private $membreType;

    /**
    * @var int
    *
    * @ORM\Column(name="level", type="integer", options={"comment":"la profondeur relative dans la matrice"})
    */
    private $level;

    /**
    * @var float
    *
    * @ORM\Column(name="value", type="float", options={"comment":"le pourcentage acquis"})
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
     * Set level
     *
     * @param integer $level
     *
     * @return PercentagePerLevel
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set value
     *
     * @param float $value
     *
     * @return PercentagePerLevel
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
     * Set membreType
     *
     * @param \AppBundle\Entity\MembreType $membreType
     *
     * @return PercentagePerLevel
     */
    public function setMembreType(\AppBundle\Entity\MembreType $membreType = null)
    {
        $this->membreType = $membreType;

        return $this;
    }

    /**
     * Get membreType
     *
     * @return \AppBundle\Entity\MembreType
     */
    public function getMembreType()
    {
        return $this->membreType;
    }
}
