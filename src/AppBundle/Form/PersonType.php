<?php

namespace AppBundle\Form;

use AppBundle\Entity\City;
use AppBundle\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

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
                'help_block' => 'Last name be automatically converted to upper case.',
            ),
        ));
        $builder->add('alias', CollectionType::class, array(
            'label' => 'Aliases',
            'required' => false,
            'entry_type' => TextType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'entry_options' => array(
                'label' => false,
            ),
            'by_reference' => false,
            'attr' => array(
                'help_block' => '',
                'class' => 'collection-simple',
            ),
        ));
        $builder->add('native', null, array(
            'label' => 'Native',
            'required' => false,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('occupation', CollectionType::class, array(
            'label' => 'Occupations',
            'required' => false,
            'required' => false,
            'entry_type' => TextType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'entry_options' => array(
                'label' => false,
            ),
            'by_reference' => false,
            'attr' => array(
                'help_block' => 'Format: Year (if known); Occupation',
                'class' => 'collection-simple',
            ),
        ));
        $builder->add('sex', ChoiceType::class, array(
            'expanded' => true,
            'multiple' => false,
            'choices' => array(
                'Female' => Person::FEMALE,
                'Male' => Person::MALE,
                'Unknown' => null,
            ),
        ));
        $builder->add('birthDate', TextType::class, array(
            'label' => 'Birth Date',
            'attr' => array(
                'help_block' => 'The closest date known for the date. YYYY-MM-DD. Use -00 for unknown month or day.',
            ),
        ));
        $builder->add('birthDateDisplay', null, array(
            'label' => 'Birth Date',
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
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('birthPlace', Select2EntityType::class, array(
            'remote_route' => 'city_typeahead',
            'class' => City::class,
            'multiple' => false,
            'primary_key' => 'id',
            'text_property' => 'name',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
        ));
        $builder->add('race');
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
