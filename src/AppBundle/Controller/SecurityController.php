<?php

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use AppBundle\Entity\Membre;

use AppBundle\Form\UserRegistrationType;

/**
* @Route("/security", name="security_")
*/
class SecurityController extends Controller{
	/**
    * @Route("/login", name="login")
    */
    public function loginAction(Request $request, AuthenticationUtils $authenticationUtils){
        
    	if($this->isGranted('IS_AUTHENTICATED_FULLY')){
    		return $this->redirectToRoute('account_index');
    	}

    	// get the login error if there is one
	    $error = $authenticationUtils->getLastAuthenticationError();

	    // last username entered by the user
	    $lastUsername = $authenticationUtils->getLastUsername();

	    return $this->render('security/login.html.twig', array(
	        'last_username' => $lastUsername,
	        'error'         => $error,
	    ));
    }


    /**
    * @Route("/logout", name="logout")
    */
    public function logoutAction(){
    	
    	return $this->redirectToRoute('homepage');
    }
}
