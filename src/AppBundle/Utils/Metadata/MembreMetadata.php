<?php
namespace AppBundle\Utils\Metadata;

use AppBundle\Utils\Metadata\Metadata;
use AppBundle\Utils\Metadata\HeaderValidator\MembreHeaderValidator;

use AppBundle\Utils\Validator\TextFieldValidator;
use AppBundle\Utils\Validator\EntityFieldValidator;
use AppBundle\Utils\Validator\ChoiceFieldValidator;
use AppBundle\Utils\Validator\ImageFieldValidator;
use AppBundle\Utils\Validator\UrlFieldValidator;
use AppBundle\Utils\Validator\DateFieldValidator;
use AppBundle\Utils\Validator\IntegerFieldValidator;
use AppBundle\Utils\Validator\EmailFieldValidator;

use AppBundle\Utils\Filter\TitleFilter;

use AppBundle\Entity\Membre;
use AppBundle\Entity\Corporation;
use AppBundle\Entity\MembreType as MembreQuality;

class MembreMetadata extends Metadata{

	public function __construct($path,$options){

		$em = $options['entity_manager'];

		$headerValidators 	= [new MembreHeaderValidator()];

		$bodyValidators 	= [
			new TextFieldValidator("Civilité",["nullable"=>false,"filters"=>[new TitleFilter()]]),

			new TextFieldValidator("Nom & Prénoms",["nullable"=>false,"filters"=>[new TitleFilter()]]),

            new EntityFieldValidator("Corporation",["nullable"=>true,"class"=>Corporation::class,"entity_manager"=>$em,"table_name"=>"Corporations"]),

            new DateFieldValidator("Date de Naissance",["nullable"=>true]),
            new EmailFieldValidator("Email",["nullable"=>true]),

            new EntityFieldValidator("Nature",["nullable"=>false,"class"=>MembreQuality::class,"entity_manager"=>$em,"table_name"=>"Forever Quality Member"]),
            new EntityFieldValidator("Code Parrain",["nullable"=>false,"search_by"=>"code","class"=>Membre::class,"entity_manager"=>$em,"table_name"=>"Parrains"]),
		];

		parent::__construct($path,$headerValidators,$bodyValidators,$options);
		$this->setDefaultSheetname("Metadonnées");
	}

    public function onData(\AppBundle\Utils\Event\Event $event){

    	$em = $this->getOption("entity_manager");
    	$membre = new Membre();

    	$fields = $this->getSheetHeader();
        $empty_cell = 0;
        $dbParrain = [];

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

                        case 'Nature':
                            $membre->setType($choice);
                        break;

                        case 'Corporation':
                            $membre->setCorporation($choice);
                        break;

                        case 'Code Parrain':

                            $membre->setParrain($choice);
                            
                        
                        break;
                	}
                }                   
            }
            else{
            	switch ($field) {
            		case 'Civilité':
            			$membre->setCivility($el->getValue());
            		break;
            		case 'Nom & Prénoms':
            		    $membre->setUsername($el->getValue());
            		break;
            		case 'Date de Naissance':
            		    $membre->setBirth($el->getStart());
            		break;
                    case 'Email':
                        $membre->setEmail($el->getValue());
                    break;
                    
            	}
            }
        }
        if($empty_cell != count($fields)) {

            $em->persist($membre);

            $rep = $em->getRepository(\AppBundle\Entity\Role::class);
            if(($role = $rep->findOneByLabel('ROLE_SUBSCRIBER'))){
                $mr = new \AppBundle\Entity\MembreRole();
                $mr->setRole($role);
                $mr->setMembre($membre);
                $em->persist($mr);
            }
        }
	}

    public function onCellData(\AppBundle\Utils\Event\Event $event){
        
    }

    public function onHeaderData(\AppBundle\Utils\Event\Event $event){

    }
}