<?php 
namespace AppBundle\EventListener\Doctrine;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use AppBundle\Entity\Membre;

class GeneralListener{

    /**
    * @var UserPasswordEncoderInterface 
    */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder = $encoder;
    }

    public function prePersist(LifecycleEventArgs $args){
        $entity = $args->getEntity();

        if ($entity instanceof Membre) {
            $plainPassword = 'bbmlm';
            $encoded = $this->encoder->encodePassword($entity, $plainPassword);
            $entity->setPassword($encoded);
            $entity->setCode(\AppBundle\Entity\Membre::generateToken(30));

            if(($el = $entity->getParrain())){
                $nbr = intval($el->getChildNbr())+1;
                $el->setChildNbr($nbr);
            }

            if(($el = $entity->getCorporation())){
                $nbr = intval($el->getMembreNbr())+1;
                $el->setMembreNbr($nbr);
            }
        }
    }

    public function preRemove(LifecycleEventArgs $args){
        $entity = $args->getEntity();
    }
}