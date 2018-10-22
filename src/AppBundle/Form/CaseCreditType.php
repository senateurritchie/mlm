<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\RequestStack;


use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use AppBundle\Entity\CaseCredit;

class CaseCreditType extends AbstractType
{
    protected $requestStack;

    public function __construct(RequestStack $requestStack){
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $idFboOpts = array(
            "mapped"=>false,
            "label"=>"Identifiant du FBO",
            "attr"=>["placeholder"=>"identifiant du fbo...","class"=>"input-sm"]
        );

        $valueOpts = array(
            "label"=>"CC",
            "attr"=>["class"=>"input-sm","placeholder"=>"nombre de Case Crédit à attribuer","min"=>1]
        );

        $dateOpts = array(
            "label"=>"Date de réalisation",
            "widget"=>"single_text",
            "attr"=>["class"=>"input-sm","placeholder"=>"date de réalisation"],
        );


        $builder
        ->add('idFbo',TextType::class,$idFboOpts)
        ->add('value',IntegerType::class,$valueOpts)
        ->add('date',DateType::class,$dateOpts)
        ->addEventListener(FormEvents::PRE_SET_DATA,function(FormEvent $event)use(&$options,&$idFboOpts,&$dateOpts){
            $model = $event->getData();
            $form = $event->getForm();

            if (!$model) {
                return;
            }

            $mode = @$options["use_for_mode"];
            $request = $this->requestStack->getCurrentRequest();

            
            if($mode == "research"){
                $idFboOpts["required"] = false;
                $dateOpts["required"] = false;
                $dateOpts2 = $dateOpts;
                $dateOpts2["mapped"] = false;
                $dateOpts2["label"] = "Date de fin";
                $dateOpts["data"] = $request->query->get("date") ? new \DateTime($request->query->get("date")) : null;
                $dateOpts2["data"] = $request->query->get("dateFin") ? new \DateTime($request->query->get("dateFin")) : null;


                $form
                ->add('idFbo',TextType::class,$idFboOpts)
                ->remove('value')
                ->add('date',DateType::class,$dateOpts)
                ->add('dateFin',DateType::class,$dateOpts2)
                ->add('order_name',ChoiceType::class,array(
                    "required"=>false,
                    "placeholder"=>"ordre alphabetique...",
                    "choices"=>[
                        "A-Z"=>"ASC",
                        "Z-A"=>"DESC",
                    ],
                    'choice_attr' => function($value, $key, $index) {
                        $attrs = [];
                        $request = $this->requestStack->getCurrentRequest();
                        if($request->query->get("order_name") == $value){
                            $attrs["selected"] = "selected";
                        }
                        return $attrs;
                    },
                    "mapped"=>false,
                ))
               ->add('order_year',ChoiceType::class,array(
                    "required"=>false,
                    "placeholder"=>"ordre chronologique...",
                    "choices"=>[
                        "croissant"=>"ASC",
                        "décroissant"=>"DESC",
                    ],
                    'choice_attr' => function($value, $key, $index) {
                        $attrs = [];
                        $request = $this->requestStack->getCurrentRequest();
                        if($request->query->get("order_year") == $value){
                            $attrs["selected"] = "selected";
                        }
                        return $attrs;
                    },
                    "mapped"=>false,
                ))
                ->add('order_id',ChoiceType::class,array(
                    "required"=>false,
                    "placeholder"=>"",
                    "label"=>"Ordre d'entrée",
                    "choices"=>[
                        "premier entré"=>"ASC",
                        "dernier entré"=>"DESC",
                    ],
                    'choice_attr' => function($value, $key, $index) {
                        $attrs = [];
                        $request = $this->requestStack->getCurrentRequest();
                        if($request->query->get("order_id") == $value){
                            $attrs["selected"] = "selected";
                        }
                        else if(!$request->query->get("order_id") && $value == "DESC"){
                            $attrs["selected"] = "selected";
                        }
                        return $attrs;
                    },
                    "mapped"=>false,
                ))
                ->add('limit',IntegerType::class,array(
                    "required"=>false,
                    "label"=>"Resultats",
                    'attr' => [
                        "value"=>$request->query->get("limit") ? intval($request->query->get("limit")) : 50,
                        "min"=>1,
                    ],
                    "mapped"=>false,
                ));
            }

            //$usernameOpts['attr']["disabled"] = "disabled";
            //$emailOpts['attr']["disabled"] = "disabled";

            //$form->add('username', TextType::class,$usernameOpts)
            //$form->add('email',EmailType::class,$emailOpts);
            //
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'use_for_mode'=>"registration",
            'data_class' => CaseCredit::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return null;
    }


}
