<?php

namespace AppBundle\Form;

use AppBundle\Entity\Notary;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * LedgerType form.
 */
class LedgerType extends AbstractType {

    /**
     * Add form fields to $builder.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('notary', Select2EntityType::class, array(
            'remote_route' => 'notary_typeahead',
            'class' => Notary::class,
            'multiple' => false,
            'primary_key' => 'id',
            'text_property' => 'name',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
        ));
        $builder->add('year', null, array(
            'label' => 'Year',
            'required' => true,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('volume', null, array(
            'label' => 'Volume',
            'required' => true,
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
            'data_class' => 'AppBundle\Entity\Ledger'
        ));
    }

}
