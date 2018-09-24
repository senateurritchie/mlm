<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\AcceptHeader;


use AppBundle\Entity\Membre;
use AppBundle\Entity\Role;
use AppBundle\Entity\MembreRole;
use AppBundle\Form\MembreAdminRegistrationType;

/**
* @Route("/admin/users", name="admin_user_")
*/
class AdminUserController extends Controller
{
	/**
    * @Route("/{user_id}", requirements={"user_id":"(\d+)?"}, name="index")
    */
    public function indexAction(Request $request,$user_id=null){
        if($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_OBSERVER'));
        else{
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, "Vous n'êtes as autorisé à consulter cette page");
        }
        
    	$em = $this->getDoctrine()->getManager();
    	$rep = $em->getRepository(Membre::class);
        $rep_role = $em->getRepository(Role::class);

    	$limit = intval($request->query->get('limit',50));
    	$offset = intval($request->query->get('offset',0));

    	$limit = $limit > 50 ? 50 : $limit;
    	$offset = $offset < 0 ? 0 : $offset;

        if($user_id){
            $request->query->set('id',intval($user_id));
        }

    	$params = $request->query->all();
    	$data = $rep->search($params,$limit,$offset);

    	$user = new Membre();
    	$form = $this->createForm(MembreAdminRegistrationType::class,$user,[
            'upload_dir' => $this->getParameter('public_upload_directory'),
        ]);

    	$form->handleRequest($request);
    	if($form->isSubmitted() && $form->isValid()){
            $user->setCreateAt(new \Datetime());

            $roles = [];

            $userrole = new MembreRole();
            $userrole->setMembre($user);
            $userrole->setRole($form->get('roles')->getData());
            $userrole->setCreateAt(new \Datetime());
            $roles[] = $userrole;

            $privileges = $form->get('privileges')->getData();

            if(count($privileges)){
                foreach ($privileges as $key => $el) {
                    $userrole = new MembreRole();
                    $userrole->setMembre($user);
                    $userrole->setRole($el);
                    $userrole->setCreateAt(new \Datetime());
                    $roles[] = $userrole;
                }
            }

    		$em->persist($user);

            foreach ($roles as $el) {
                $em->persist($el);
            }


    		$em->flush();
    		$this->addFlash('notice-success',1);

    		return $this->redirectToRoute("admin_user_index");
    	}

        // requete ajax
        if($request->isXmlHttpRequest()){

            $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'));
            
            if(intval(@$params['id'])){
                if(empty($data)){
                    throw $this->createNotFoundException("Element introuvable");
                }
                $data = $data[0];
            }


            $result = array();
            $json = json_decode($this->get("serializer")->serialize($data,'json',array('groups' => array('group1'))),true);
            $result['model'] = $json;
            $result['view'] = "";
            $view = null;

            if(is_array($data)){
                $view = $this->render('account/admin/user/item-render.html.twig',array(
                    "data"=>$data,
                ));
            }
            else{

                $form2 = $this->createForm(MembreAdminRegistrationType::class,$data,[
                    'usr_roles'=>$data->getUroles(),
                    'upload_dir' => $this->getParameter('public_upload_directory'),
                   	"action"=>$this->generateUrl("admin_user_update",["user_id"=>$data->getId()])
                ]);
                
                $formView = $form2->createView();

                $view = $this->render('account/admin/user/selected-view.html.twig',[
                    "data"=>$data,
                    "use_modal"=>"update",
                    "form"=>$formView,
                ]);
            }

            if ($acceptHeader->has('text/html')) {
                $item = $acceptHeader->get('text/html');
                return $view;
            }
            
            if($view){
                $result['view'] = $view->getContent();
            }

            $json = json_encode($result);
            $response = new Response($json);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $currentUser = null;

        if($user_id){
            $currentUser = array(
                "data"=>null,
                "stats"=>[
                    "hierachical_parents"=>0,
                    "children"=>0,
                    "direct_children"=>0,
                    "indirect_children"=>0,
                    "direct_children_with_nodes"=>0,
                    "direct_children_leaft"=>0,
                    'indirect_children_leaft'=>0,
                    "indirect_children_with_nodes"=>0,
                    "generations"=>0
                ]
            );


            $rep = $em->getRepository(\AppBundle\Entity\Matrice::class);
            if(($node = $rep->findOneBy(["membre"=>$data[0]]))){

                $currentUser['data'] = $node;

                // les parrains hierachiques
                $parrains = $rep->count([
                    'is_reference_parent'=>true,
                    "ref_left"=>$node->getLeftInd(),
                    "ref_right"=>$node->getRightInd(),
                ]);
                $currentUser["stats"]['hierachical_parents'] = $parrains;

                // les filleuls directs
                $fd = $rep->count([
                    'is_under_reference'=>true,
                    "ref_left"=>$node->getLeftInd(),
                    "ref_right"=>$node->getRightInd(),
                    "depth"=>$node->getDepth() +1,
                ]);
                $currentUser["stats"]['direct_children'] = $fd;

                // les filleuls indirects
                $fd = $rep->count([
                    'is_under_reference'=>true,
                    'is_indirect_child'=>true,
                    "ref_left"=>$node->getLeftInd(),
                    "ref_right"=>$node->getRightInd(),
                    "ref_depth"=>$node->getDepth()+1,
                ]);
                $currentUser["stats"]['indirect_children'] = $fd;

                // les filleuls direct ayants aussi des filleuls
                $fd = $rep->count([
                    'is_under_reference'=>true,
                    'is_node_child'=>true,
                    'is_direct_child'=>true,
                    "ref_left"=>$node->getLeftInd(),
                    "ref_right"=>$node->getRightInd(),
                    "ref_depth"=>$node->getDepth()+1,
                ]);
                $currentUser["stats"]['direct_children_with_nodes'] = $fd;

                // les filleuls direct n'ayants pas des filleuls
                $fd = $rep->count([
                    'is_under_reference'=>true,
                    'is_direct_child'=>true,
                    'is_leaft_child'=>true,
                    "ref_left"=>$node->getLeftInd(),
                    "ref_right"=>$node->getRightInd(),
                    "ref_depth"=>$node->getDepth()+1,
                ]);
                $currentUser["stats"]['direct_children_leaft'] = $fd;


                // les filleuls indirect n'ayants pas des filleuls
                $fd = $rep->count([
                    'is_under_reference'=>true,
                    'is_indirect_child'=>true,
                    'is_leaft_child'=>true,
                    "ref_left"=>$node->getLeftInd(),
                    "ref_right"=>$node->getRightInd(),
                    "ref_depth"=>$node->getDepth()+1,
                ]);
                $currentUser["stats"]['indirect_children_leaft'] = $fd;


                // les filleuls indirect ayants aussi des filleuls
                $fd = $rep->count([
                    'is_under_reference'=>true,
                    'is_node_child'=>true,
                    'is_indirect_child'=>true,
                    "ref_left"=>$node->getLeftInd(),
                    "ref_right"=>$node->getRightInd(),
                    "ref_depth"=>$node->getDepth()+1,
                ]);
                $currentUser["stats"]['indirect_children_with_nodes'] = $fd;

                // tout les membres du reseau 
                $fd = $rep->count([
                    'is_under_reference'=>true,
                    "ref_left"=>$node->getLeftInd(),
                    "ref_right"=>$node->getRightInd(),
                ]);
                $currentUser["stats"]['children'] = $fd;

                // le nombre de generation dans le reseau 
                $fd = $rep->generationCount([
                    'is_under_reference'=>true,
                    "ref_left"=>$node->getLeftInd(),
                    "ref_right"=>$node->getRightInd(),
                ]);
                $currentUser["stats"]['generations'] =  $fd ? $fd - intval($node->getDepth()) : 0;

                

                
            }
        }

    	return $this->render('account/admin/user/index.html.twig',array(
            "users"=>$data,
            "currentUser"=>$currentUser,
            "roles"=>$rep_role->findAll(),
    		"form"=>$form->createView()
    	));
    }

    /**
    * @Route("/{user_id}/update", requirements={"user_id":"\d+"}, name="update")
    * @Method("POST")
    */
    public function updateAction(Request $request,$user_id){
        // protection par role
        if($this->isGranted('ROLE_ADMIN'));
        else{
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, "Vous ne pouvez pas éffectuer cette action");
        }

        $em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository(Membre::class);
        $result = ["status"=>false];

        if(!($item = $rep->find($user_id))){
            throw $this->createNotFoundException();
        }

        $cloned = clone($item);
        $oldImage = $item->getImage();

        $form = $this->createForm(MembreAdminRegistrationType::class,$item,[
            'usr_roles'=>$item->getUroles(),
            'upload_dir' => $this->getParameter('public_upload_directory'),
        ]);

        $form->handleRequest($request);
        
        foreach ($form->all() as $child) {
            if (!$child->isValid() && count($child->getErrors())) {
                $formatted = '['.$child->getName().']: '.$child->getErrors()[0]->getMessage();
                $this->addFlash('notice-error',$formatted);
            }
        }

        if($form->isSubmitted() && $form->isValid()){
            $date = new \Datetime();
            $em->merge($item);

            if(!$item->getImage() && $oldImage){
                $item->setImage($oldImage);
            }
            
            $em->flush();

            if($oldImage && $item->getImage() && $item->getImage() != $oldImage){
                $path = $this->getParameter('public_upload_directory').'/'.basename($oldImage);
                unlink($path);
            }

            $this->addFlash('notice-success',"Mise à jour éffectuée avec succes");
            return $this->redirectToRoute('admin_user_index');
        }
        return $this->redirectToRoute('admin_user_index');
    }
    

    /**
    * @Route("/{user_id}/grant-role", requirements={"user_id":"\d+"}, name="grant_role")
    * @Method("POST")
    */
    public function grantAction(Request $request,$user_id){
        // protection par role
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, "Vous ne pouvez pas éffectuer cette action");


        $em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository(Membre::class);
        $rep_role = $em->getRepository(Role::class);
        $result = ["status"=>false];
        $role_id = intval($request->request->get("role_id"));


        if(!($user = $rep->find($user_id))){
            throw $this->createNotFoundException();
        }

        if(!($role = $rep_role->find($role_id))){
            throw $this->createNotFoundException();
        }

        $roles = $user->getRoles();

        if(!in_array($role->getLabel(), $roles)){
            if($role->getType() != "role"){
                $userrole = new MembreRole();
                $userrole->setMembre($user);
                $userrole->setRole($role);
                $userrole->setCreateAt(new \Datetime());
                $em->persist($userrole);
                $em->flush();
                $result['status'] = true;
                $result['message'] = "modification effectuée avec succès";
            }
        }

        return $this->json($result);
    }

    /**
    * @Route("/{user_id}/revoke-role", requirements={"user_id":"\d+"}, name="revoke_role")
    * @Method("POST")
    */
    public function revokeAction(Request $request,$user_id){
        // protection par role
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, "Vous ne pouvez pas éffectuer cette action");

        $em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository(Membre::class);
        $rep_role = $em->getRepository(Role::class);
        $result = ["status"=>false];
        $role_id = intval($request->request->get("role_id"));


        if(!($user = $rep->find($user_id))){
            throw $this->createNotFoundException();
        }

        if(!($role = $rep_role->find($role_id))){
            throw $this->createNotFoundException();
        }

        $roles = $user->getRoles();

        if(in_array($role->getLabel(), $roles)){
            if($role->getType() != "role"){
                $rep = $em->getRepository(MembreRole::class);

                if(($userrole = $rep->findOneBy(["user"=>$user,"role"=>$role]))) {
                    $em->remove($userrole);
                    $em->flush();
                    $result['status'] = true;
                    $result['message'] = "modification effectuée avec succès";
                }
            }
        }

        return $this->json($result);
    }

    /**
    * @Route("/metadata/upload", name="metadata_upload")
    */
    public function metadataUploadAction(Request $request){
       
        // protection par role
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Vous ne pouvez pas éffectuer cette action');

        $em = $this->getDoctrine()->getManager();

        if(!$request->files->has('file')){
        	$this->addFlash('notice-error',"Veuillez envoyer le fichier à générer");
        	return $this->redirectToRoute('admin_user_index');
        }

        $file = $request->files->get('file');

        if(!in_array($file->guessExtension(), ["zip"])){
            $this->addFlash('notice-error',"Veuillez envoyer un fichier type zip");
        	return $this->redirectToRoute('admin_user_index');
        }

        $fileName = md5(uniqid()).'.'.$file->guessExtension();
        $file->move($this->getParameter('public_upload_directory'), $fileName);
        $file_path = $this->getParameter('public_upload_directory').'/'.$fileName;


        $reader = new \AppBundle\Utils\Metadata\MembreMetadata($file_path,array(
            "entity_manager"=>$em,
        ));

        $reader
        ->on("error",function($event)use(&$result){
            $result['errors'] = $event->getValue();
            $this->addFlash('notice-error',$event->getValue());
        });

        try {
            $em->getConnection()->beginTransaction();
            $reader->process();
            $em->flush();
            $this->addFlash('notice-success',"opération effectuée avec succes");
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            $this->addFlash('notice-error',$e->getMessage());
        }

        @unlink($file_path);

        //return new Response('ok');
        return $this->redirectToRoute("admin_user_index");
    }

}
