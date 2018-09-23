<?php 
namespace AppBundle\EventListener\Doctrine;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpFoundation\File\File;

use AppBundle\Entity\Membre;

use AppBundle\Services\FileUploader;
use AppBundle\Services\PrivateFileUploader;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GeneralUploadListener{
    /**
    * @var AppBundle\Services\FileUploader
    */
    private $uploader;
    /**
    * @var AppBundle\Services\FileUploader
    */
    private $pvtUploader;

    public function __construct(ContainerInterface $container){
        $this->uploader = $container->get('app.uploader');
        $this->pvtUploader = $container->get('app.prv_uploader');
    }

    public function prePersist(LifecycleEventArgs $args){
        $entity = $args->getEntity();
        $this->uploadFile($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args){
        $entity = $args->getEntity();
        $this->uploadFile($entity);
    }

    public function postLoad(LifecycleEventArgs $args){

        $entity = $args->getEntity();
        $fileName = null;
        if($entity instanceof Membre){

        }
    }

    public function postRemove(LifecycleEventArgs $args){
        $entity = $args->getEntity();

        if ($entity instanceof Membre) {
            if(($fileName = trim($entity->getImage()))){
                $this->uploader->remove($fileName);
            }
        }
    }

    private function uploadFile($entity){
        if ($entity instanceof Membre) {
            $file = $entity->getImage();

            // only upload new files
            if ($file instanceof UploadedFile) {
                $fileName = $this->uploader->upload($file);
                $entity->setImage($fileName);
            }
        }
    }
}