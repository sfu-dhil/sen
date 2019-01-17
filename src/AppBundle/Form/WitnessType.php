<?php

namespace AppBundle\Form;

use AppBundle\Entity\Event;
use AppBundle\Entity\Person;
use AppBundle\Entity\WitnessCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * WitnessType form.
 */
class WitnessType extends AbstractType {

    /**
     * Add form fields to $builder.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('category', EntityType::class, array(
            'class' => WitnessCategory::class,
        ));

        $builder->add('person', Select2EntityType::class, array(
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

        $builder->add('event', EntityType::class, array(
            'class' => Event::class,
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
            'data_class' => 'AppBundle\Entity\Witness'
        ));
    }

}
