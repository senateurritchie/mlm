<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\HttpFoundation\File\File;



use AppBundle\Form\MembreType;
use AppBundle\Entity\Role;

class MembreAdminRegistrationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('salt')->remove('createAt')->remove('password');

        $privilegeOpts = array(
            "required"=>false,
            "class"=>Role::class,
            "choice_label"=>"name",
            "mapped"=>false,
            "multiple"=>true,
            "expanded"=>true,
            'group_by' => function($index, $key, $value) {
                $e = explode("_", $value);
                if(count($e) >= 2){
                    $e = array_slice($e, 0,2);
                    $e = implode("_", $e);
                }
                else{
                    $e = $value;
                }
                return $e;
            },
            'query_builder' => function (EntityRepository $er) {
                $qb = $er->createQueryBuilder('r');

                $qb->where($qb->expr()->eq('r.type',':role'))
                ->setParameter('role',"privilege")
                ->orderBy('r.name', 'ASC');

                return $qb;
            },
        );

        $rolesOpts = array(
            "placeholder"=>"",
            "mapped"=>false,
            "class"=>Role::class,
            "choice_label"=>function($value){
                return ucwords($value->getName());
            },
            "attr"=>["class"=>"input-sm"],
            'query_builder' => function (EntityRepository $er) {
                $qb = $er->createQueryBuilder('r');

                $qb->where($qb->expr()->eq('r.type',':role'))
                ->setParameter('role',"role")
                ->orderBy('r.name', 'ASC');
                return $qb;
            },
        );


         $typeOpts = array(
            "label"=>"Qualité",
            "class"=>\AppBundle\Entity\MembreType::class,
            "choice_label"=>"name",
            "attr"=>["class"=>"input-sm"],
            'query_builder' => function (EntityRepository $er) {
                $qb = $er->createQueryBuilder('r')
                ->orderBy('r.name', 'ASC');
                return $qb;
            },
        );

        $usernameOpts = array(
            "label"=>"Nom & Prénoms",
            "attr"=>["class"=>"input-sm"]
        );

        $emailOpts = array(
            "attr"=>["class"=>"input-sm"]
        );

        $builder
        ->add('codeParrain', TextType::class,array(
            "attr"=>["placeholder"=>"code parrainage","class"=>"input-sm parrain-code"],
            "mapped"=>false,
            "required"=>false
        ))
        ->add('civility', ChoiceType::class,array(
            "attr"=>["class"=>"input-sm"],
            "label"=>"Civilité",
            "choices"=>[
                "M"=>"Monsieur",
                "Mme"=>"Madame",
                "Mlle"=>"mademoiselle",
            ],
        ))
        ->add('birth', DateType::class,array(
            "attr"=>["class"=>"input-sm"],
            "label"=>"Date de naissance",
            "widget"=>"single_text"
        ))
        ->add('username', TextType::class,$usernameOpts)
        ->add('email',EmailType::class,$emailOpts)
        ->add('roles',EntityType::class,$rolesOpts)
        ->add('type',EntityType::class,$typeOpts)
        ->add('privileges',EntityType::class,$privilegeOpts)
        ->addEventListener(FormEvents::PRE_SET_DATA,function(FormEvent $event)use(&$options,&$privilegeOpts,&$rolesOpts,&$usernameOpts,&$emailOpts,&$typeOpts){
            $model = $event->getData();
            $form = $event->getForm();

            if (!$model) {
                return;
            }

            $uroles = $options["usr_roles"];
            $roles = [];
            $privils = [];

            foreach ($uroles as $key => $el) {
                if($el->getRole()->getType() == "role"){
                    $roles[] = $el->getRole();
                }
                else if($el->getRole()->getType() == "privilege"){
                    $privils[] = $el->getRole();
                }
            }

            if(count($privils)){
                $privilegeOpts['choice_attr'] = function($value, $key, $index)use(&$privils) {
                    $attrs = [];
                    foreach ($privils as $el) {
                        if($el->getId() == $value->getId()){
                            $attrs["checked"] = "checked";
                        }
                    }
                    return $attrs;
                };
                $form->add('privileges',EntityType::class,$privilegeOpts);
            }

            if(count($roles)){
                $rolesOpts['attr'] = [
                    "disabled"=>"disabled"
                ];

                $rolesOpts['choice_attr'] = function($value, $key, $index)use(&$roles) {
                    $attrs = [];
                    foreach ($roles as $el) {
                        if($el->getId() == $value->getId()){
                            $attrs["selected"] = "selected";
                        }
                    }
                    return $attrs;
                };
                $form->add('roles',EntityType::class,$rolesOpts);
            }

            //$usernameOpts['attr']["disabled"] = "disabled";
            //$emailOpts['attr']["disabled"] = "disabled";

            //$form->add('username', TextType::class,$usernameOpts)
            //$form->add('email',EmailType::class,$emailOpts);
            //



            if($model->getImage()){
                $path = $options['upload_dir'].'/'.basename($model->getImage());
                $model->setImage(new File($path));
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return MembreType::class;
    }


}
