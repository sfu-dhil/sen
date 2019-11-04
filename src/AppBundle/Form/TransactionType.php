<?php

namespace AppBundle\Form;

use AppBundle\Entity\Ledger;
use AppBundle\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * TransactionType form.
 */
class TransactionType extends AbstractType {

    /**
     * Add form fields to $builder.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('date', DateType::class, array(
            'label' => 'Date',
            'required' => true,
            'html5' => true,
            'widget' => 'single_text',
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('category');
        $builder->add('ledger', Select2EntityType::class, array(
            'remote_route' => 'ledger_typeahead',
            'class' => Ledger::class,
            'multiple' => false,
            'primary_key' => 'id',
            'text_property' => 'name',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
        ));

        $builder->add('page', null, array(
            'label' => 'Page',
            'required' => true,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('firstParty', Select2EntityType::class, array(
            'remote_route' => 'person_typeahead',
            'class' => Person::class,
            'multiple' => false,
            'primary_key' => 'id',
            'text_property' => 'name',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
        ));
        $builder->add('firstPartyNote', null, array(
            'label' => 'First Party Note',
            'required' => true,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('conjunction', null, array(
            'label' => 'Conjunction',
            'required' => true,
            'attr' => array(
                'help_block' => 'One of "to", "from", "by", "and".',
            ),
        ));
        $builder->add('secondParty', Select2EntityType::class, array(
            'remote_route' => 'person_typeahead',
            'class' => Person::class,
            'multiple' => false,
            'primary_key' => 'id',
            'text_property' => 'name',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
        ));
        $builder->add('secondPartyNote', null, array(
            'label' => 'Second Party Note',
            'required' => true,
            'attr' => array(
                'help_block' => '',
            ),
        ));

        $builder->add('notes', null, array(
            'label' => 'Transaction Notes',
            'required' => false,
            'attr' => array(
                'help_block' => '',
            ),
        ));
    }

    /**
     * Define options for the form.
     *
     * Set default, optional, and required options passed to the
     * buildForm() method via the $options parameter.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Transaction'
        ));
    }

}
