<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * TransactionType form.
 */
class TransactionType extends AbstractType
{
    /**
     * Add form fields to $builder.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        $builder->add('date', null, array(
            'label' => 'Date',
            'required' => true,
            'attr' => array(
                'help_block' => '',
            ),
        ));
                $builder->add('page', null, array(
            'label' => 'Page',
            'required' => true,
            'attr' => array(
                'help_block' => '',
            ),
        ));
                $builder->add('notes', null, array(
            'label' => 'Notes',
            'required' => false,
            'attr' => array(
                'help_block' => '',
            ),
        ));
                $builder->add('firstPartyNote', null, array(
            'label' => 'First Party Note',
            'required' => true,
            'attr' => array(
                'help_block' => '',
            ),
        ));
                $builder->add('conjunction', null, array(
            'label' => 'Conjunction',
            'required' => true,
            'attr' => array(
                'help_block' => '',
            ),
        ));
                $builder->add('secondPartyNote', null, array(
            'label' => 'Second Party Note',
            'required' => true,
            'attr' => array(
                'help_block' => '',
            ),
        ));
                        $builder->add('firstParty');
                        $builder->add('secondParty');
                        $builder->add('category');
                        $builder->add('ledger');
        
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
            'data_class' => 'AppBundle\Entity\Transaction'
        ));
    }

}
