<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
* @Route("/account", name="account_")
*/
class AccountController extends Controller
{
	/**
    * @Route("/", name="index")
    */
    public function indexAction(){

        return $this->redirectToroute('admin_index');

    	#return $this->render('account/index.html.twig');
    }
}
