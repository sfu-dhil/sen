<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use App\Entity\Race;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Race form.
 */
class RaceType extends AbstractType {
    /**
     * Add form fields to $builder.
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('name', TextType::class, [
            'label' => 'Name',
            'required' => true,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('label', TextType::class, [
            'label' => 'Label',
            'required' => true,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('description', TextareaType::class, [
            'label' => 'Description',
            'required' => false,
            'attr' => [
                'help_block' => '',
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('spanishUngendered', TextType::class, [
            'label' => 'Spanish Ungendered',
            'required' => true,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('spanishMale', TextType::class, [
            'label' => 'Spanish Male',
            'required' => true,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('spanishFemale', TextType::class, [
            'label' => 'Spanish Female',
            'required' => true,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('frenchUngendered', TextType::class, [
            'label' => 'French Ungendered',
            'required' => true,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('frenchMale', TextType::class, [
            'label' => 'French Male',
            'required' => true,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('frenchFemale', TextType::class, [
            'label' => 'French Female',
            'required' => true,
            'attr' => [
                'help_block' => '',
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
            'data_class' => Race::class,
        ]);
    }
}
