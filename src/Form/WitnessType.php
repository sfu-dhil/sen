<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use App\Entity\Event;
use App\Entity\Person;
use App\Entity\Witness;
use App\Entity\WitnessCategory;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Witness form.
 */
class WitnessType extends AbstractType {
    /**
     * Add form fields to $builder.
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('category', Select2EntityType::class, [
            'label' => 'WitnessCategory',
            'class' => WitnessCategory::class,
            'remote_route' => 'witness_category_typeahead',
            'allow_clear' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'witness_category_new_popup',
                'add_label' => 'Add WitnessCategory',
            ],
        ]);

        $builder->add('person', Select2EntityType::class, [
            'label' => 'Person',
            'class' => Person::class,
            'remote_route' => 'person_typeahead',
            'allow_clear' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'person_new_popup',
                'add_label' => 'Add Person',
            ],
        ]);

        $builder->add('event', Select2EntityType::class, [
            'label' => 'Event',
            'class' => Event::class,
            'remote_route' => 'event_typeahead',
            'allow_clear' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'event_new_popup',
                'add_label' => 'Add Event',
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
            'data_class' => Witness::class,
        ]);
    }
}
