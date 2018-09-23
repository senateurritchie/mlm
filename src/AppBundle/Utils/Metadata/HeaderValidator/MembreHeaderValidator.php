<?php
namespace AppBundle\Utils\Metadata\HeaderValidator;
use AppBundle\Utils\Metadata\HeaderValidator\HeaderValidator;

class MembreHeaderValidator extends HeaderValidator{
	
	public function __construct(){
		$fields = [
			"Civilité","Nom & Prénoms","Corporation","Date de Naissance","Email","Nature","Code Parrain",
		];
		
		parent::__construct($fields);
	}
}

					
