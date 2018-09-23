<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Corporation
 *
 * @ORM\Table(name="corporation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CorporationRepository")
 */
class Corporation
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
    * @var string
    *
    * @Gedmo\Slug(fields={"name"})
    * @ORM\Column(name="slug", type="string", length=100, unique=true)
    */
    private $slug;

    /**
     * @var int
     *
     * @ORM\Column(name="membre_nbr", type="integer")
     */
    private $membreNbr = 0;

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
     * Set name
     *
     * @param string $name
     *
     * @return Corporation
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Corporation
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set membreNbr
     *
     * @param integer $membreNbr
     *
     * @return Corporation
     */
    public function setMembreNbr($membreNbr)
    {
        $this->membreNbr = $membreNbr;

        return $this;
    }

    /**
     * Get membreNbr
     *
     * @return int
     */
    public function getMembreNbr()
    {
        return $this->membreNbr;
    }

    /**
     * Set createAt
     *
     * @param \DateTime $createAt
     *
     * @return Corporation
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
}

