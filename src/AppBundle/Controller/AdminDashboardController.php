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
* @Route("/admin/dashboard", name="admin_dashboard_")
*/
class AdminDashboardController extends Controller
{
	/**
    * @Route("/", name="index")
    */
    public function indexAction(Request $request){
        if($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_OBSERVER'));
        else{
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, "Vous n'êtes as autorisé à consulter cette page");
        }
        
    	$em = $this->getDoctrine()->getManager();
    	$rep = $em->getRepository(Matrice::class);
    	
    	return $this->render('account/admin/dashboard/index.html.twig',array());
    }

}
