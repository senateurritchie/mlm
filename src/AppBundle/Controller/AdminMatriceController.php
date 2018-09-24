<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\AcceptHeader;


use AppBundle\Entity\Matrice;

/**
* @Route("/admin/node", name="admin_node_")
*/
class AdminMatriceController extends Controller
{
	/**
    * @Route("/{node_id}/{search_mode}", requirements={"node_id":"\d+","search_mode":"[\w-]+"}, name="index")
    */
    public function indexAction(Request $request,$node_id,$search_mode){
        if($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_OBSERVER'));
        else{
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, "Vous n'êtes as autorisé à consulter cette page");
        }
        
    	$em = $this->getDoctrine()->getManager();
    	$rep = $em->getRepository(Matrice::class);

    	$limit = intval($request->query->get('limit',50));
    	$offset = intval($request->query->get('offset',0));

    	$limit = $limit > 50 ? 50 : $limit;
    	$offset = $offset < 0 ? 0 : $offset;

    	$params = $request->query->all();

        if(!($node = $rep->find($node_id))){
            throw $this->createNotFoundException("ce noeud n'existe pas dans la matrice");
        }

        switch ($search_mode) {

            // les parrains ascendants
            case 1:
                $params["is_reference_parent"] = true;
                $params["ref_left"] = $node->getLeftInd();
                $params["ref_right"] = $node->getRightInd();
            break;

            // Filleuls directs
            case 2:
                $params["is_under_reference"] = true;
                $params["ref_left"] = $node->getLeftInd();
                $params["ref_right"] = $node->getRightInd();
                $params["depth"] = $node->getDepth() +1;
            break;

            // Filleuls directs sans reseau
            case 3:
                $params["is_under_reference"] = true;
                $params["is_direct_child"] = true;
                $params["is_leaft_child"] = true;
                $params["ref_left"] = $node->getLeftInd();
                $params["ref_right"] = $node->getRightInd();
                $params["ref_depth"] = $node->getDepth() +1;
            break;

            // Filleuls directs avec reseau
            case 4:
                $params["is_under_reference"] = true;
                $params["is_node_child"] = true;
                $params["is_direct_child"] = true;
                $params["ref_left"] = $node->getLeftInd();
                $params["ref_right"] = $node->getRightInd();
                $params["ref_depth"] = $node->getDepth() +1;
            break;

            // Filleuls indirects
            case 5:
                $params["is_under_reference"] = true;
                $params["is_indirect_child"] = true;
                $params["ref_left"] = $node->getLeftInd();
                $params["ref_right"] = $node->getRightInd();
                $params["ref_depth"] = $node->getDepth() +1;
            break;

            // Filleuls indirects sans reseau
            case 6:
                $params["is_under_reference"] = true;
                $params["is_indirect_child"] = true;
                $params["is_leaft_child"] = true;
                $params["ref_left"] = $node->getLeftInd();
                $params["ref_right"] = $node->getRightInd();
                $params["ref_depth"] = $node->getDepth() +1;
            break;

            // Filleuls indirects avec reseau
            case 7:
                $params["is_under_reference"] = true;
                $params["is_node_child"] = true;
                $params["is_indirect_child"] = true;
                $params["ref_left"] = $node->getLeftInd();
                $params["ref_right"] = $node->getRightInd();
                $params["ref_depth"] = $node->getDepth() +1;
            break;

            // Tous le reseau
            case 8:
                $params["is_under_reference"] = true;
                $params["ref_left"] = $node->getLeftInd();
                $params["ref_right"] = $node->getRightInd();
            break;
        }

    	$data = $rep->search($params,$limit,$offset);

        // requete ajax
        if($request->isXmlHttpRequest()){

            $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'));

            $result = array();
            $json = json_decode($this->get("serializer")->serialize($data,'json',array('groups' => array('group1'))),true);

            $result['model'] = $json;

            $result['view'] = "";

            $view = null;

            $view = $this->render('account/admin/user/item-render.html.twig',array(
                "data"=>$data,
            ));

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

    	return $this->render('account/admin/matrice/index.html.twig',array(
            "node"=>$node,
            "data"=>$data,
    	));
    }

}
