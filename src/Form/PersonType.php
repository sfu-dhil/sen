<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use App\Entity\City;
use App\Entity\Person;
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
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('firstName', null, [
            'label' => 'First Name',
            'required' => true,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('lastName', null, [
            'label' => 'Last Name',
            'required' => true,
            'attr' => [
                'help_block' => 'Last name be automatically converted to upper case.',
            ],
        ]);
        $builder->add('alias', CollectionType::class, [
            'label' => 'Aliases',
            'required' => false,
            'entry_type' => TextType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'entry_options' => [
                'label' => false,
            ],
            'by_reference' => false,
            'attr' => [
                'help_block' => '',
                'class' => 'collection-simple',
            ],
        ]);
        $builder->add('native', null, [
            'label' => 'Native',
            'required' => false,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('occupation', CollectionType::class, [
            'label' => 'Occupations',
            'required' => false,
            'required' => false,
            'entry_type' => TextType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'entry_options' => [
                'label' => false,
            ],
            'by_reference' => false,
            'attr' => [
                'help_block' => 'Format: Year (if known); Occupation',
                'class' => 'collection-simple',
            ],
        ]);
        $builder->add('sex', ChoiceType::class, [
            'expanded' => true,
            'multiple' => false,
            'choices' => [
                'Female' => Person::FEMALE,
                'Male' => Person::MALE,
                'Unknown' => null,
            ],
        ]);
        $builder->add('birthDate', TextType::class, [
            'label' => 'Birth Date',
            'attr' => [
                'help_block' => 'The closest date known for the date. YYYY-MM-DD. Use -00 for unknown month or day.',
            ],
        ]);
        $builder->add('birthDateDisplay', null, [
            'label' => 'Birth Date',
            'attr' => [
                'help_block' => 'Descriptive date. bef 1790, abt 2 Mar 1780. If it is empty, birthDate will be displayed instead.',
            ],
        ]);
        $builder->add('birthStatus', null, [
            'label' => 'Birth Status',
            'required' => false,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('status', null, [
            'label' => 'Status',
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('birthPlace', Select2EntityType::class, [
            'remote_route' => 'city_typeahead',
            'class' => City::class,
            'multiple' => false,
            'primary_key' => 'id',
            'text_property' => 'name',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
        ]);
        $builder->add('race');
    }

    /**
     * Define options for the form.
     *
     * Set default, optional, and required options passed to the
     * buildForm() method via the $options parameter.
     */
    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Person',
        ]);
    }
}
