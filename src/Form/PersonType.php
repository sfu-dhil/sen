<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use App\Entity\City;
use App\Entity\Person;
use App\Entity\Race;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Person form.
 */
class PersonType extends AbstractType {
    /**
     * Add form fields to $builder.
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('firstName', TextType::class, [
            'label' => 'First Name',
            'required' => true,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('lastName', TextType::class, [
            'label' => 'Last Name',
            'required' => true,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('alias', CollectionType::class, [
            'label' => 'Aliases',
            'required' => true,
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'entry_type' => TextType::class,
            'entry_options' => [
                'label' => false,
            ],
            'by_reference' => false,
            'attr' => [
                'class' => 'collection collection-simple',
                'help_block' => '',
            ],
        ]);
        $builder->add('native', TextType::class, [
            'label' => 'Native',
            'required' => false,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('occupation', CollectionType::class, [
            'label' => 'Occupations',
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'entry_type' => TextType::class,
            'entry_options' => [
                'label' => false,
            ],
            'by_reference' => false,
            'attr' => [
                'class' => 'collection collection-simple',
                'help_block' => 'Format: Year (if known); Occupation',
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
            'required' => false,
            'attr' => [
                'help_block' => 'The closest date known for the date. YYYY-MM-DD. Use -00 for unknown month or day.',
            ],
        ]);
        $builder->add('writtenBirthDate', TextType::class, [
            'label' => 'Written Birth Date',
            'required' => false,
            'attr' => [
                'help_block' => 'Descriptive date. bef 1790, abt 2 Mar 1780.',
            ],
        ]);
        $builder->add('birthStatus', TextType::class, [
            'label' => 'Birth Status',
            'required' => false,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('status', TextType::class, [
            'label' => 'Status',
            'required' => false,
            'attr' => [
                'help_block' => '',
            ],
        ]);

        $builder->add('birthPlace', Select2EntityType::class, [
            'label' => 'Birth Place',
            'class' => City::class,
            'remote_route' => 'city_typeahead',
            'allow_clear' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'city_new_popup',
                'add_label' => 'Add City',
            ],
        ]);

        $builder->add('race', Select2EntityType::class, [
            'label' => 'Race',
            'class' => Race::class,
            'remote_route' => 'race_typeahead',
            'allow_clear' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'race_new_popup',
                'add_label' => 'Add Race',
            ],
        ]);
    }

    /**
     * Define options for the form.
     *
     * Set default, optional, and required options passed to the
     * buildForm() method via the $options parameter.
     */
    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => Person::class,
        ]);
    }
}
