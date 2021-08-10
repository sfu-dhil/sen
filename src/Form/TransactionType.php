<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use App\Entity\Ledger;
use App\Entity\Person;
use App\Entity\Transaction;
use App\Entity\TransactionCategory;
use App\Repository\TransactionCategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Transaction form.
 */
class TransactionType extends AbstractType {
    /**
     * Add form fields to $builder.
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('date', DateType::class, [
            'label' => 'Date',
            'required' => true,
            'widget' => 'single_text',
            'html5' => true,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('category', EntityType::class, [
            'label' => 'Category',
            'class' => TransactionCategory::class,
            'choice_label' => 'label',
            'expanded' => false,
            'multiple' => false,
            'query_builder' => static fn(TransactionCategoryRepository $repo) => $repo->createQueryBuilder('c')->orderBy('c.label'),
        ]);
        $builder->add('ledger', Select2EntityType::class, [
            'label' => 'Ledger',
            'class' => Ledger::class,
            'required' => true,
            'remote_route' => 'ledger_typeahead',
            'allow_clear' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'ledger_new_popup',
                'add_label' => 'Add Ledger',
            ],
        ]);

        $builder->add('page', null, [
            'label' => 'Page',
            'required' => true,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('firstParty', Select2EntityType::class, [
            'label' => 'First Party',
            'class' => Person::class,
            'remote_route' => 'person_typeahead',
            'allow_clear' => true,
            'required' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'person_new_popup',
                'add_label' => 'Add Person',
            ],
        ]);
        $builder->add('firstPartyNote', TextType::class, [
            'label' => 'First Party Note',
            'required' => false,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('conjunction', ChoiceType::class, [
            'label' => 'Conjunction',
            'required' => true,
            'multiple' => false,
            'expanded' => false,
            'choices' => [
                '[Blank]' => null,
                'And' => 'and',
                'To' => 'to',
            ],
            'empty_data' => null,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('secondParty', Select2EntityType::class, [
            'label' => 'Second Party',
            'class' => Person::class,
            'remote_route' => 'person_typeahead',
            'allow_clear' => true,
            'required' => false,
            'attr' => [
                'help_block' => '',
                'add_path' => 'person_new_popup',
                'add_label' => 'Add Person',
            ],
        ]);
        $builder->add('secondPartyNote', TextType::class, [
            'label' => 'Second Party Note',
            'required' => false,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('notes', TextType::class, [
            'label' => 'Notes',
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
            'data_class' => Transaction::class,
        ]);
    }
}
