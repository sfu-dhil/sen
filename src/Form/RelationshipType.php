<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use App\Entity\Person;
use App\Entity\Relationship;
use App\Entity\RelationshipCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Relationship form.
 */
class RelationshipType extends AbstractType {
    /**
     * Add form fields to $builder.
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('startDate', TextType::class, [
            'label' => 'Start Date',
            'required' => false,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('endDate', TextType::class, [
            'label' => 'End Date',
            'required' => false,
            'attr' => [
                'help_block' => '',
            ],
        ]);

        $builder->add('category', Select2EntityType::class, [
            'label' => 'Category',
            'class' => RelationshipCategory::class,
            'remote_route' => 'relationship_category_typeahead',
            'allow_clear' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'relationship_category_new_popup',
                'add_label' => 'Add Category',
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

        $builder->add('relation', Select2EntityType::class, [
            'label' => 'Relation',
            'class' => Person::class,
            'remote_route' => 'person_typeahead',
            'allow_clear' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'person_new_popup',
                'add_label' => 'Add Person',
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
            'data_class' => Relationship::class,
        ]);
    }
}
