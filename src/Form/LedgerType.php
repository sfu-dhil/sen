<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use App\Entity\Ledger;
use App\Entity\Notary;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Ledger form.
 */
class LedgerType extends AbstractType {
    /**
     * Add form fields to $builder.
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('notary', Select2EntityType::class, [
            'label' => 'Notary',
            'class' => Notary::class,
            'remote_route' => 'notary_typeahead',
            'allow_clear' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'notary_new_popup',
                'add_label' => 'Add Notary',
            ],
        ]);
        $builder->add('volume', TextType::class, [
            'label' => 'Volume',
            'required' => true,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('year', null, [
            'label' => 'Year',
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
            'data_class' => Ledger::class,
        ]);
    }
}
