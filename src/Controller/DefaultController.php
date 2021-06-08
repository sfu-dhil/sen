<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Default controller.
 *
 * Handles the home page.
 */
class DefaultController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Home page action.
     *
     * @Route("/", name="homepage")
     * @Template
     */
    public function indexAction() {
        // replace this example code with whatever you need
        return [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . \DIRECTORY_SEPARATOR,
        ];
    }

    /**
     * @Route("/privacy", name="privacy")
     * @Template
     */
    public function privacyAction(Request $request) : void {
    }
}
