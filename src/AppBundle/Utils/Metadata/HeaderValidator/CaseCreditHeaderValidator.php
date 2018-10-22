<?php
namespace AppBundle\Utils\Metadata\HeaderValidator;
use AppBundle\Utils\Metadata\HeaderValidator\HeaderValidator;

class CaseCreditHeaderValidator extends HeaderValidator{
	
	public function __construct(){
		$fields = [
			"Nom & Prénoms","Identifiant","Case Crédit","Date"
		];
		
		parent::__construct($fields);
	}
}

					
