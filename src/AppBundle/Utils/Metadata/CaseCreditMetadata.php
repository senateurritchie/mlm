<?php
namespace AppBundle\Utils\Metadata;

use AppBundle\Utils\Metadata\Metadata;
use AppBundle\Utils\Metadata\HeaderValidator\CaseCreditHeaderValidator;

use AppBundle\Utils\Validator\TextFieldValidator;
use AppBundle\Utils\Validator\EntityFieldValidator;
use AppBundle\Utils\Validator\DateFieldValidator;
use AppBundle\Utils\Validator\IntegerFieldValidator;

use AppBundle\Utils\Filter\TitleFilter;

use AppBundle\Entity\Membre;
use AppBundle\Entity\CaseCredit;

class CaseCreditMetadata extends Metadata{

	public function __construct($path,$options){

		$em = $options['entity_manager'];

		$headerValidators 	= [new CaseCreditHeaderValidator()];

		$bodyValidators 	= [
			new TextFieldValidator("Nom & Prénoms",["nullable"=>true,"filters"=>[new TitleFilter()]]),
            new EntityFieldValidator("Identifiant",["nullable"=>false,"search_by"=>"code","class"=>Membre::class,"entity_manager"=>$em,"table_name"=>"Parrains"]),

            new IntegerFieldValidator("Case Crédit",["nullable"=>false]),
            new DateFieldValidator("Date",["nullable"=>false]),
		];

		parent::__construct($path,$headerValidators,$bodyValidators,$options);
		$this->setDefaultSheetname("Metadonnées");
	}

    public function onData(\AppBundle\Utils\Event\Event $event){

        $em = $this->getOption("entity_manager");
        $admin = $this->getOption("current_admin");

    	$cc = new CaseCredit();

    	$fields = $this->getSheetHeader();
        $empty_cell = 0;

        foreach ($event->getValue() as $pos_f => $el) {
        	$field = $fields[$pos_f];

        	if(!trim($el->getValue())){
                $empty_cell++;
                continue;
            }

            else if($el instanceof \AppBundle\Utils\MetadataEntry\MetadataEntityEntry){
                $choices = $el->getChoices();
                foreach ($choices as $i=>$choice) {

                    switch ($field) {
                        case 'Identifiant':
                            $cc->setFbo($choice);
                        break;
                	}
                }
            }
            else{
            	switch ($field) {
                    case 'Case Crédit':
                        $cc->setValue($el->getValue());
                    break;
            		case 'Date':
            		    $cc->setDate($el->getStart());
            		break;
            	}
            }
        }
        if($empty_cell != count($fields)) {
            $cc->setAdmin($admin);
            $em->persist($cc);
        }
	}

    public function onCellData(\AppBundle\Utils\Event\Event $event){
        
    }

    public function onHeaderData(\AppBundle\Utils\Event\Event $event){

    }
}