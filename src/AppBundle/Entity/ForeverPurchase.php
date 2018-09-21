<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * ForeverPurchase
 *
 * @ORM\Table(name="forever_purchase", options={"comment":"enregistre les achats forever"})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ForeverPurchaseRepository")
 */
class ForeverPurchase
{
    /**
    * @var int
    * @Groups({"group1","group2"})
    * @ORM\Column(name="id", type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
    * @var AppBundle\Entity\Membre
    
    * @Groups({"group2"})
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Membre")
    * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
    */
    private $membre;

    /**
    * @var AppBundle\Entity\ForeverPack
    *
    * @Groups({"group2"})
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ForeverPack")
    * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
    */
    private $pack;

    /**
    * @var AppBundle\Entity\Membre
    *
    * @Groups({"group2"})
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Membre")
    * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
    */
    private $admin;

    /**
    * @var \DateTime
    *
    * @Groups({"group1","group2"})
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
     * Set createAt
     *
     * @param \DateTime $createAt
     *
     * @return ForeverPurchase
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
     * @return ForeverPurchase
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

    /**
     * Set pack
     *
     * @param \AppBundle\Entity\ForeverPack $pack
     *
     * @return ForeverPurchase
     */
    public function setPack(\AppBundle\Entity\ForeverPack $pack = null)
    {
        $this->pack = $pack;

        return $this;
    }

    /**
     * Get pack
     *
     * @return \AppBundle\Entity\ForeverPack
     */
    public function getPack()
    {
        return $this->pack;
    }

    /**
     * Set admin
     *
     * @param \AppBundle\Entity\Membre $admin
     *
     * @return ForeverPurchase
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
}
