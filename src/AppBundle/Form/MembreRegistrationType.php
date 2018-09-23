<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;


use AppBundle\Form\MembreType;

class MembreRegistrationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('salt')->remove('createAt')->remove('password')->remove('roles');

        $builder
        ->add('email', EmailType::class,array(
            "label"=>"Adresse email",
        ))
        ->add('username', TextType::class,array(
            "label"=>"Nom",
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return MembreType::class;
    }

}
