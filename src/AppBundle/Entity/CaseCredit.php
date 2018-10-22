<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * CaseCredit
 *
 * @ORM\Table(name="case_credit")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CaseCreditRepository")
 */
class CaseCredit
{
    /**
    * @var int
    *
    * @Groups({"group1","group2"})
    * @ORM\Column(name="id", type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
    * @var AppBundle\Entity\Membre
    * 
    * @Groups({"group1","group2"})
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Membre")
    * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
    */
    private $fbo;

    /**
    * @var AppBundle\Entity\Membre
    * 
    * @Groups({"group1","group2"})
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Membre")
    * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
    */
    private $admin;

    /**
    * @var int
    *
    * @ORM\Column(name="value", type="integer", options={"comment":"le nombre de cases crédits réalisé"})
    */
    private $value = 0;

    /**
    * @var \DateTime
    *
    * @ORM\Column(name="date", type="date", options={"comment":"date à laquelle les cases crédit ont été réellement éffectuées"})
    */
    private $date;

    /**
    * @var \DateTime
    *
    * @Gedmo\Timestampable(on="create")
    * @ORM\Column(name="create_at", type="datetime", options={"comment":"date à laquelle les cases crédit ont été ajoutées à cette plateforme"})
    */
    private $createAt;


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
     * @param integer $value
     *
     * @return CaseCredit
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set createAt
     *
     * @param \DateTime $createAt
     *
     * @return CaseCredit
     */
    public function setCreateAt($createAt)
    {
        $this->createAt = $createAt;

        return $this;
    }

    /**
     * Get createAt
     *
     * @return \DateTime
     */
    public function getCreateAt()
    {
        return $this->createAt;
    }

    /**
     * Set fbo
     *
     * @param \AppBundle\Entity\Membre $fbo
     *
     * @return CaseCredit
     */
    public function setFbo(\AppBundle\Entity\Membre $fbo = null)
    {
        $this->fbo = $fbo;

        return $this;
    }

    /**
     * Get fbo
     *
     * @return \AppBundle\Entity\Membre
     */
    public function getFbo()
    {
        return $this->fbo;
    }

    /**
     * Set admin
     *
     * @param \AppBundle\Entity\Membre $admin
     *
     * @return CaseCredit
     */
    public function setAdmin(\AppBundle\Entity\Membre $admin = null)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get admin
     *
     * @return \AppBundle\Entity\Membre
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return CaseCredit
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}
