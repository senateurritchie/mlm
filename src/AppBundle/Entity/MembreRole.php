<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * MembreRole
 *
 * @ORM\Table(name="membre_role")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MembreRoleRepository")
 */
class MembreRole
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
    *
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Membre", inversedBy="uroles")
    * @ORM\JoinColumn(onDelete="CASCADE")
    */
    private $membre;

    /**
    * @var AppBundle\Entity\Role
    *
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Role", inversedBy="membres")
    * @ORM\JoinColumn(onDelete="CASCADE")
    */
    private $role;

    /**
    * @var \DateTime
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
     * @return MembreRole
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
     * @return MembreRole
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
     * Set role
     *
     * @param \AppBundle\Entity\Role $role
     *
     * @return MembreRole
     */
    public function setRole(\AppBundle\Entity\Role $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \AppBundle\Entity\Role
     */
    public function getRole()
    {
        return $this->role;
    }
}
