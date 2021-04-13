<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use App\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * RelationshipType form.
 */
class RelationshipType extends AbstractType
{
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
        $builder->add('category');
        $builder->add('person', Select2EntityType::class, [
            'remote_route' => 'person_typeahead',
            'class' => Person::class,
            'multiple' => false,
            'primary_key' => 'id',
            'text_property' => 'name',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
        ]);
        $builder->add('relation', Select2EntityType::class, [
            'remote_route' => 'person_typeahead',
            'class' => Person::class,
            'multiple' => false,
            'primary_key' => 'id',
            'text_property' => 'name',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
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
            'data_class' => 'App\Entity\Relationship',
        ]);
    }
}
