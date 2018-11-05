<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * PersonType form.
 */
class PersonType extends AbstractType {

    /**
     * Add form fields to $builder.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('firstName', null, array(
            'label' => 'First Name',
            'required' => true,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('lastName', null, array(
            'label' => 'Last Name',
            'required' => true,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('alias', null, array(
            'label' => 'Alias',
            'required' => true,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('native', null, array(
            'label' => 'Native',
            'required' => false,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('occupation', null, array(
            'label' => 'Occupation',
            'required' => false,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('sex', null, array(
            'label' => 'Sex',
            'required' => false,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('birthDate', null, array(
            'label' => 'Birth Date',
            'required' => true,
            'attr' => array(
                'help_block' => 'The closest date known for the date. YYYY-MM-DD. Use -00 for unknown month or day.',
            ),
        ));
        $builder->add('birthDateDisplay', null, array(
            'label' => 'Birth Date',
            'required' => true,
            'attr' => array(
                'help_block' => 'Descriptive date. bef 1790, abt 2 Mar 1780. If it is empty, birthDate will be displayed instead.',
            ),
        ));
        $builder->add('birthStatus', null, array(
            'label' => 'Birth Status',
            'required' => false,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('status', null, array(
            'label' => 'Status',
            'required' => true,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('birthPlace');
        $builder->add('race');
        $builder->add('relationships');
        $builder->add('events');
    }

    /**
     * Define options for the form.
     *
     * Set default, optional, and required options passed to the
     * buildForm() method via the $options parameter.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Person'
        ));
    }

}
