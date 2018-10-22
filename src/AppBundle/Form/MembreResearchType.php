<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\RequestStack;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


use AppBundle\Entity\Membre;
use AppBundle\Form\MembreType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class MembreResearchType extends AbstractType
{
    protected $requestStack;
    protected $em;

    public function __construct(RequestStack $requestStack, \Doctrine\Common\Persistence\ObjectManager $em){
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $request = $this->requestStack->getCurrentRequest();

        if(($defaultv = $request->query->get("corporation"))){
            $rep = $this->em->getRepository(\AppBundle\Entity\Corporation::class);
            if(!($defaultv = $rep->findOneBySlug($defaultv))){

            }
        }

        $builder
        ->add('corporation',EntityType::class,array(
            "required"=>false,
            "class"=>\AppBundle\Entity\Corporation::class,
            "choice_label"=>"name",
            "choice_value"=>"slug",
            "mapped"=>false,
            "by_reference"=>false,

            "attr"=>["class"=>"input-sm"],
            'choice_attr' => function($value, $key, $index)use(&$request) {
                $attrs = [];
                if($request->query->get("corporation") == $value->getSlug()){
                    $attrs["selected"] = "selected";
                }
                return $attrs;
            },
            "data"=>$defaultv,
        ))
        ->add('quality', ChoiceType::class,array(
            "required"=>false,
            "mapped"=>false,
            "label"=>"Qualité",
            "choices"=>[
                "NOVUS CUSTOMER"=>"NC",
                "ANIMATEUR ADJOINT"=>"AA",
                "ANIMATEUR"=>"A",
                "MANAGER ADJOINT"=>"MA",
                "MANAGER"=>"M",
            ],
            "attr"=>["class"=>"input-sm"],
            "data"=>$request->query->get("quality") ? $request->query->get("quality") : null,
        ))
        ->add('code', TextType::class,array(
            "required"=>false,
            "mapped"=>false,
            "label"=>"Identifiant",
            "data"=>$request->query->get("code") ? $request->query->get("code") : null,
            "attr"=>["class"=>"input-sm"],
        ))
        ->add('parrain_code', TextType::class,array(
            "required"=>false,
            "mapped"=>false,
            "label"=>"Identifiant parrain",
            "data"=>$request->query->get("parrain_code") ? $request->query->get("parrain_code") : null,
            "attr"=>["class"=>"input-sm"],
        ))
        ->add('date', DateType::class,array(
            "required"=>false,
            "mapped"=>false,
            "label"=>"Date d'adhésion",
            "widget"=>"single_text",
            "attr"=>["class"=>"input-sm"],
            "data"=>$request->query->get("date") ? new \DateTime($request->query->get("date")) : null,
        ))
        ->add('dateFin', DateType::class,array(
            "required"=>false,
            "mapped"=>false,
            "label"=>"Interval",
            "widget"=>"single_text",
            "attr"=>["class"=>"input-sm"],
            "data"=>$request->query->get("dateFin") ? new \DateTime($request->query->get("dateFin")) : null,
        ))
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

     /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null
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
