<?php

namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\MembreProfilType;


/**
* @Route("/admin", name="admin_")
*/
class AdminController extends Controller
{
	/**
    * @Route("/", name="index")
    */
    public function indexAction(){
        $em = $this->getDoctrine()->getManager();
    	return $this->render('account/admin/index.html.twig');
    }

    /**
    * @Route("/profil", name="profil")
    */
    public function profilAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        $item = $this->getUser();
        $oldImage = $item->getImage();

        $contacts = new \Doctrine\Common\Collections\ArrayCollection();

        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($item->getContacts() as $contact) {
            $contacts->add($contact);
        }

        $form = $this->createForm(MembreProfilType::class,$item,[
            'upload_dir' => $this->getParameter('public_upload_directory'),
        ]);

        $form->handleRequest($request);

        foreach ($form->all() as $child) {
            if (!$child->isValid() && count($child->getErrors())) {
                $error = '['.$child->getName().']: '.$child->getErrors()[0]->getMessage();
                $this->addFlash('notice-error',$error);
            }
        }
        
        if($form->isSubmitted() && $form->isValid()){
            $em->merge($item);

            if(!$item->getImage() && $oldImage){
                $item->setImage($oldImage);
            }

            if($oldImage && $item->getImage() && $item->getImage() != $oldImage){
                $path = $this->getParameter('public_upload_directory').'/'.$oldImage;
                unlink($path);
            }

            $item = $form->getData();
           // gestion des suppressions
            foreach ($contacts as $contact) {
                if (false === $item->getContacts()->contains($contact)) {
                    // remove the Task from the Tag
                    $em->remove($contact);
                }
            }

            $em->flush();

            $this->addFlash('notice-success',"modification éffectuée avec success");
            return $this->redirectToRoute("admin_profil",['tab'=>"settings"]);
        }

        return $this->render('account/profil/index.html.twig',[
            "form"=>$form->createView()
        ]);
    }
}
