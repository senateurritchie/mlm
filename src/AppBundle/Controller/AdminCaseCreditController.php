<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\AcceptHeader;

use AppBundle\Entity\Membre;
use AppBundle\Entity\CaseCredit;
use AppBundle\Form\CaseCreditType;

/**
* @Route("/admin/cc", name="admin_cc_")
*/
class AdminCaseCreditController extends Controller
{
	/**
    * @Route("/{cc_id}", requirements={"cc_id":"(\d+)?"}, name="index")
    */
    public function indexAction(Request $request,$cc_id=null){
        if($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_OBSERVER'));
        else{
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, "Vous n'êtes as autorisé à consulter cette page");
        }

       
        
    	$em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository(CaseCredit::class);
        $rep_user = $em->getRepository(Membre::class);

    	$limit = intval($request->query->get('limit',50));
    	$offset = intval($request->query->get('offset',0));

    	$limit = $limit > 50 ? 50 : $limit;
    	$offset = $offset < 0 ? 0 : $offset;

        if($cc_id){
            $request->query->set('id',intval($cc_id));
        }

    	$params = $request->query->all();
    	$data = $rep->search($params,$limit,$offset);

    	$cc = new CaseCredit();
    	$form = $this->createForm(CaseCreditType::class,$cc);

    	$form->handleRequest($request);
    	if($form->isSubmitted() && $form->isValid()){

            $idFbo = $form->get('idFbo')->getData();

            if(!($fbo = $rep_user->findOneByCode($idFbo))){
                $this->addFlash('notice-success',"l'identifiant saisi n'existe pas dans la base de données");
                return $this->redirectToRoute("admin_cc_index");
            }

            $cc->setFbo($fbo);
            $cc->setAdmin($this->getUser());
    		$em->persist($cc);
    		$em->flush();
    		$this->addFlash('notice-success',"Case Crédit ajouté avec succes");
    		return $this->redirectToRoute("admin_cc_index");
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
                $view = $this->render('account/admin/case-credit/item-render.html.twig',array(
                    "data"=>$data,
                ));
            }
            else{

                $form2 = $this->createForm(CaseCreditType::class,$data,[
                   	"action"=>$this->generateUrl("admin_cc_update",["cc_id"=>$data->getId()])
                ]);
                
                $formView = $form2->createView();

                $view = $this->render('account/admin/case-credit/selected-view.html.twig',[
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

        $formResearch = $this->createForm(CaseCreditType::class,new CaseCredit(),[
            "use_for_mode"=>"research"
        ]);

    	return $this->render('account/admin/case-credit/index.html.twig',array(
            "storage"=>$data,
            "form"=>$form->createView(),
            "form_search"=>$formResearch->createView()
    	));
    }

    /**
    * @Route("/{cc_id}/update", requirements={"cc_id":"\d+"}, name="update")
    * @Method("POST")
    */
    public function updateAction(Request $request,$cc_id){
        // protection par role
        if($this->isGranted('ROLE_ADMIN'));
        else{
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, "Vous ne pouvez pas éffectuer cette action");
        }

        $em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository(CaseCredit::class);
        $result = ["status"=>false];

        if(!($item = $rep->find($cc_id))){
            throw $this->createNotFoundException();
        }

        $cloned = clone($item);
        $oldImage = $item->getImage();

        $form = $this->createForm(CaseCreditType::class,$item);

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
            $em->flush();

            $this->addFlash('notice-success',"Mise à jour éffectuée avec succes");
            return $this->redirectToRoute('admin_user_index');
        }
        return $this->redirectToRoute('admin_user_index');
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

        $reader = new \AppBundle\Utils\Metadata\CaseCreditMetadata($file_path,array(
            "entity_manager"=>$em,
            "current_admin"=>$this->getUser(),
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
        return $this->redirectToRoute("admin_cc_index");
    }

}
