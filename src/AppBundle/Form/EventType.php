<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * EventType form.
 */
class EventType extends AbstractType
{
    /**
     * Add form fields to $builder.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        $builder->add('writtenDate', null, array(
            'label' => 'Written Date',
            'required' => false,
            'attr' => array(
                'help_block' => '',
            ),
        ));
                $builder->add('date', null, array(
            'label' => 'Date',
            'required' => false,
            'attr' => array(
                'help_block' => '',
            ),
        ));
                $builder->add('note', null, array(
            'label' => 'Note',
            'required' => false,
            'attr' => array(
                'help_block' => '',
            ),
        ));
                        $builder->add('category');
                        $builder->add('participants');
                        $builder->add('location');
        
    }
    
    /**
     * Define options for the form.
     *
     * Set default, optional, and required options passed to the
     * buildForm() method via the $options parameter.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Event'
        ));
    }

}
