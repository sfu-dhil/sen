<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form\Mapper;

use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\DataMapper\PropertyPathMapper;
use Symfony\Component\Form\FormInterface;

class OccupationMapper extends PropertyPathMapper implements DataMapperInterface {
    public function mapDataToForms($viewData, $forms) : void {
        if ( ! $viewData) {
            return;
        }
        if ( ! is_array($viewData)) {
            throw new UnexpectedTypeException($viewData, 'array');
        }

        parent::mapDataToForms($viewData, $forms);

        /** @var FormInterface[] $formData */
        $formData = iterator_to_array($forms);
        $formData['date']->setData($viewData['date'] ?? null);
        $formData['occupation']->setData($viewData['occupation']);
    }

    public function mapFormsToData($forms, &$viewData) : void {
        parent::mapFormsToData($forms, $viewData);
        /** @var FormInterface[] $formData */
        $formData = iterator_to_array($forms);
        $viewData = [
            'date' => $formData['date']->getData(),
            'occupation' => $formData['occupation']->getData(),
        ];
    }
}
