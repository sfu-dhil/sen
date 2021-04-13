<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use App\Entity\Ledger;
use App\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * TransactionType form.
 */
class TransactionType extends AbstractType
{
    /**
     * Add form fields to $builder.
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('date', DateType::class, [
            'label' => 'Date',
            'required' => true,
            'html5' => true,
            'widget' => 'single_text',
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('category');
        $builder->add('ledger', Select2EntityType::class, [
            'remote_route' => 'ledger_typeahead',
            'class' => Ledger::class,
            'multiple' => false,
            'primary_key' => 'id',
            'text_property' => 'name',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
        ]);

        $builder->add('page', null, [
            'label' => 'Page',
            'required' => true,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('firstParty', Select2EntityType::class, [
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
        $builder->add('firstPartyNote', null, [
            'label' => 'First Party Note',
            'required' => true,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('conjunction', null, [
            'label' => 'Conjunction',
            'required' => true,
            'attr' => [
                'help_block' => 'One of "to", "from", "by", "and".',
            ],
        ]);
        $builder->add('secondParty', Select2EntityType::class, [
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
        $builder->add('secondPartyNote', null, [
            'label' => 'Second Party Note',
            'required' => true,
            'attr' => [
                'help_block' => '',
            ],
        ]);

        $builder->add('notes', null, [
            'label' => 'Transaction Notes',
            'required' => false,
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
            'data_class' => 'App\Entity\Transaction',
        ]);
    }
}
