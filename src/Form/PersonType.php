<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use App\Entity\Person;
use App\Entity\BirthStatus;
use App\Entity\Race;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            'required' => false,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('lastName', TextType::class, [
            'label' => 'Last Name',
            'required' => false,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('titles', CollectionType::class, [
            'label' => 'Titles',
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
                'help_block' => '',
            ],
        ]);
        $builder->add('aliases', CollectionType::class, [
            'label' => 'Aliases',
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

        $builder->add('occupations', CollectionType::class, [
            'label' => 'Occupations',
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'entry_type' => OccupationType::class,
            'entry_options' => [
                'label' => false,
            ],
            'by_reference' => false,
            'attr' => [
                'class' => 'collection collection-simple',
                'help_block' => '',
            ],
        ]);

        $builder->add('sex', TextType::class, [
            'label' => 'Sex',
            'required' => false,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('statuses', CollectionType::class, [
            'label' => 'Statuses',
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
                'help_block' => '',
            ],
        ]);
        $builder->add('writtenRaces', CollectionType::class, [
            'label' => 'Written Races',
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
                'help_block' => '',
            ],
        ]);

        $builder->add('birthStatus', Select2EntityType::class, [
            'label' => 'BirthStatus',
            'required' => false,
            'class' => BirthStatus::class,
            'remote_route' => 'birth_status_typeahead',
            'allow_clear' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'birth_status_new_popup',
                'add_label' => 'Add BirthStatus',
            ],
        ]);

        $builder->add('race', Select2EntityType::class, [
            'label' => 'Race',
            'required' => false,
            'class' => Race::class,
            'remote_route' => 'race_typeahead',
            'allow_clear' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'race_new_popup',
                'add_label' => 'Add Race',
            ],
        ]);
        $builder->add('notes', TextareaType::class, [
            'label' => 'Notes',
            'required' => false,
            'attr' => [
                'help_block' => 'Private research notes',
                'class' => 'tinymce',
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
