<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Matrice
 *
 * @ORM\Table(name="matrice", options={"comment":"l'arbre du reseau MLM"})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MatriceRepository")
 */
class Matrice
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
    * @var AppBundle\Entity\Membre
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Membre"))
    * @ORM\JoinColumn(onDelete="CASCADE")
    */
    private $membre;

    /**
    * @var int
    *
    * @ORM\Column(name="left_ind", type="integer", nullable=true, options={"comment":"l'indice gauche du noeud"})
    */
    private $leftInd;

    /**
    * @var int
    *
    * @ORM\Column(name="right_ind", type="integer", nullable=true,options={"comment":"l'indice droit du noeud"})
    */
    private $rightInd;

    /**
    * @var int
    *
    * @ORM\Column(name="depth", type="integer", options={"comment":"le niveau ou la profondeur du noeud dans la matrice"})
    */
    private $depth;

    /**
    * @var \DateTime
    *
    * @Gedmo\Timestampable(on="create")
    * @ORM\Column(name="create_at", type="datetime")
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
    * Set leftInd
    *
    * @param integer $leftInd
    *
    * @return Matrice
    */
    public function setLeftInd($leftInd)
    {
        $this->leftInd = $leftInd;

        return $this;
    }

    /**
    * Get leftInd
    *
    * @return int
    */
    public function getLeftInd()
    {
        return $this->leftInd;
    }

    /**
    * Set rightInd
    *
    * @param integer $rightInd
    *
    * @return Matrice
    */
    public function setRightInd($rightInd)
    {
        $this->rightInd = $rightInd;

        return $this;
    }

    /**
    * Get rightInd
    *
    * @return int
    */
    public function getRightInd()
    {
        return $this->rightInd;
    }

    /**
    * Set depth
    *
    * @param integer $depth
    *
    * @return Matrice
    */
    public function setDepth($depth)
    {
        $this->depth = $depth;

        return $this;
    }

    /**
    * Get depth
    *
    * @return int
    */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
    * Set createAt
    *
    * @param \DateTime $createAt
    *
    * @return Matrice
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
    * Set membre
    *
    * @param \AppBundle\Entity\Membre $membre
    *
    * @return Matrice
    */
    public function setMembre(\AppBundle\Entity\Membre $membre = null)
    {
        $this->membre = $membre;

        return $this;
    }

    /**
    * Get membre
    *
    * @return \AppBundle\Entity\Membre
    */
    public function getMembre()
    {
        return $this->membre;
    }
}
