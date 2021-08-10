<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use App\Entity\Event;
use App\Entity\EventCategory;
use App\Entity\Location;
use App\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Event form.
 */
class EventType extends AbstractType {
    /**
     * Add form fields to $builder.
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('writtenDate', TextType::class, [
            'label' => 'Written Date',
            'required' => false,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('date', TextType::class, [
            'label' => 'Date',
            'required' => false,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('note', TextType::class, [
            'label' => 'Note',
            'required' => false,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('recordSource', TextType::class, [
            'label' => 'Record Source',
            'required' => false,
            'attr' => [
                'help_block' => '',
            ],
        ]);

        $builder->add('category', Select2EntityType::class, [
            'label' => 'Category',
            'class' => EventCategory::class,
            'remote_route' => 'event_category_typeahead',
            'allow_clear' => true,
            'required' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'event_category_new_popup',
                'add_label' => 'Add Category',
            ],
        ]);

        $builder->add('location', Select2EntityType::class, [
            'label' => 'Location',
            'class' => Location::class,
            'remote_route' => 'location_typeahead',
            'allow_clear' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'location_new_popup',
                'add_label' => 'Add Location',
            ],
        ]);
        $builder->add('participants', Select2EntityType::class, [
            'label' => 'Participants',
            'class' => Person::class,
            'remote_route' => 'person_typeahead',
            'allow_clear' => true,
            'multiple' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'person_new_popup',
                'add_label' => 'Add Person',
            ],
        ]);

        $builder->add('witnesses', CollectionType::class, [
            'label' => 'Witnesses',
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'entry_type' => EventWitnessType::class,
            'entry_options' => [
                'label' => false,
            ],
            'by_reference' => false,
            'attr' => [
                'class' => 'collection collection-complex',
                'help_block' => ''
            ]
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
            'data_class' => Event::class,
        ]);
    }
}
