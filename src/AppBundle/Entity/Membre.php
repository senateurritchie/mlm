<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Membre
 *
 * @ORM\Table(name="membre", options={"comment":"enregistre les utilisateurs de la plateforme avec différents niveau d'acces"})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MembreRepository")
 * @UniqueEntity("email", message="cet adresse est déja enregistrée")
 */
class Membre implements UserInterface, EquatableInterface, \Serializable
{
    /**
    * @var int
    *
    * @Groups({"group1"})
    * @ORM\Column(name="id", type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
    * @var AppBundle\Entity\MembreType
    *
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MembreType")
    * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
    */
    private $type;

    /**
    * @var string
    * @Groups({"group1"})
    * @Assert\NotBlank
    * @Assert\Length(min=3, max=30)
    * @ORM\Column(name="username", type="string", length=30)
    */
    private $username;

    /**
    * @var string
    *
    * @Groups({"group1"})
    * @Assert\Email
    * @ORM\Column(name="email", type="string", length=255, nullable=true, unique=true)
    */
    private $email;

    /**
    * @var string
    *
    * @Groups({"group1"})
    * @ORM\Column(name="about_me", type="text", nullable=true)
    */
    private $aboutMe;

    /**
    * @var string
    *
    * @Groups({"group1","group2"})
    * @ORM\Column(name="image", type="string", length=255, nullable=true)
    * @assert\Image(mimeTypes={"image/jpg","image/jpeg","image/png"},minWidth=640,maxWidth=640, minHeight=360,maxHeight=360)
    */
    private $image;

    /**
    * @var string
    *
    * @Groups({"group3"})
    * @ORM\Column(name="salt", type="string", length=64, nullable=true)
    */
    private $salt;

    /**
    * @var string
    *
    * @Groups({"group3"})
    * @Assert\Length(min=8)
    * @ORM\Column(name="password", type="string", length=70)
    */
    private $password;
    /**
    * @var string
    *
    * @Groups({"group1"})
    * @ORM\Column(name="state", type="string", options={"comment":"le status d'un utilisateur"}, columnDefinition="ENUM('activate','pending','blocked')", nullable=true)
    */
    private $state = "pending";
    /**
    * @var string
    *
    * @Groups({"group3"})
    * @ORM\Column(name="code", type="string", options={"comment":"le code de parrainage de l'utilisateur"}, nullable=true, length=64)
    */
    private $code;
    /**
    * @var string
    *
    * @Groups({"group1","group2","group3"})
    * @ORM\Column(name="locale", type="string", options={"comment":"la langue de l'utilisateur"}, nullable=true, length=5)
    */
    private $locale = 'fr';
    
    /**
    * @var array
    *
    * @Groups({"group1"})
    */
    private $roles;

    /**
    * @var Role
    *
    * @Groups({"group1","group2"})
    */
    private $masterRole;
    /**
    * @var array<Role>
    *
    * @Groups({"group1","group2"})
    */
    private $privileges = array();

    /**
    * @var \DateTime
    *
    * @Groups({"group1"})
    * @Gedmo\Timestampable(on="create")
    * @ORM\Column(name="create_at", type="datetime", nullable=true)
    */
    private $createAt;

    /**
    * @ORM\OneToMany(targetEntity="AppBundle\Entity\MembreRole", mappedBy="membre")
    * @Groups({"group4"})
    */
    private $uroles;


    /**
    * Constructor
    */
    public function __construct($username=null, $email=null,$password=null, $salt=null,$createAt=null){
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->salt = $salt;

        $this->uroles = new \Doctrine\Common\Collections\ArrayCollection();
    }



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
     * Set username
     *
     * @param string $username
     *
     * @return Membre
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Membre
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return Membre
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Membre
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return Membre
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        $uroles = $this->getUroles();
        $roles = [];

        foreach ($uroles as $key => $el) {
            $roles[] = $el->getRole();
        }

        // role principal
        $role = array_filter($roles,function($el){
            return ($el->getType() == "role");
        });
        $role = array_values($role);

        if(count($role)){
            $this->setMasterRole($role[0]);
        };

        // privileges
        $privileges = array_filter($roles,function($el){
            return ($el->getType() == "privilege");
        });
        $privileges = array_values($privileges);
        $this->setPrivileges($privileges);

        $roles = array_map(function($el){
            return $el->getLabel();
        }, $roles);

        $this->setRoles($roles);

        return $this->roles;
    }

    public function setMasterRole(Role $masterRole){
        $this->masterRole = $masterRole;
        return $this;
    }

    public function setPrivileges(array $roles = array()){
        $this->privileges = $roles;
        return $this;
    }

    public function getMasterRole(){
        return $this->masterRole;
    }

    public function getPrivileges(){
        return $this->privileges;
    }

    public function eraseCredentials()
    {
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof WebserviceUser) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    /** @see \Serializable::serialize() */
    public function serialize(){
        return serialize(array(
            $this->id,
            $this->email,
            $this->username,
            $this->password,
            $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized){
        list (
            $this->id,
            $this->email,
            $this->password,
            $this->salt
        ) = unserialize($serialized);
    }

    /**
     * Set createAt
     *
     * @param \DateTime $createAt
     *
     * @return Membre
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
     * Set state
     *
     * @param string $state
     *
     * @return Membre
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set signUpToken
     *
     * @param string $signUpToken
     *
     * @return Membre
     */
    public function setSignUpToken($signUpToken)
    {
        $this->signUpToken = $signUpToken;

        return $this;
    }

    /**
     * Get signUpToken
     *
     * @return string
     */
    public function getSignUpToken()
    {
        return $this->signUpToken;
    }

    public static function generateToken($length = 8){
        return substr(trim(base64_encode(bin2hex(openssl_random_pseudo_bytes(64,$ok))),"="),0,$length);
    }
    
    /**
     * Add urole
     *
     * @param \AppBundle\Entity\MembreRole $urole
     *
     * @return Membre
     */
    public function addUrole(\AppBundle\Entity\MembreRole $urole)
    {
        $this->uroles[] = $urole;

        return $this;
    }

    /**
     * Remove urole
     *
     * @param \AppBundle\Entity\MembreRole $urole
     */
    public function removeUrole(\AppBundle\Entity\MembreRole $urole)
    {
        $this->uroles->removeElement($urole);
    }

    /**
     * Get uroles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUroles()
    {
        return $this->uroles;
    }

    /**
     * Set aboutMe
     *
     * @param string $aboutMe
     *
     * @return Membre
     */
    public function setAboutMe($aboutMe)
    {
        $this->aboutMe = $aboutMe;

        return $this;
    }

    /**
     * Get aboutMe
     *
     * @return string
     */
    public function getAboutMe()
    {
        return $this->aboutMe;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Membre
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set locale
     *
     * @param string $locale
     *
     * @return Membre
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

   
   

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Membre
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set type
     *
     * @param \AppBundle\Entity\MembreType $type
     *
     * @return Membre
     */
    public function setType(\AppBundle\Entity\MembreType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \AppBundle\Entity\MembreType
     */
    public function getType()
    {
        return $this->type;
    }
}
