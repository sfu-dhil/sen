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
use App\Entity\Residence;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Residence form.
 */
class ResidenceType extends AbstractType {
    /**
     * Add form fields to $builder.
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('date', TextType::class, [
            'label' => 'Date',
            'required' => false,
            'attr' => [
                'help_block' => '',
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

        $builder->add('city', Select2EntityType::class, [
            'label' => 'City',
            'class' => City::class,
            'remote_route' => 'city_typeahead',
            'allow_clear' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'city_new_popup',
                'add_label' => 'Add City',
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
            'data_class' => Residence::class,
        ]);
    }
}
