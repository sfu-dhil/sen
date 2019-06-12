<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Default controller.
 *
 * Handles the home page.
 */
class DefaultController extends Controller {

    /**
     * Home page action.
     *
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction() {
        // replace this example code with whatever you need
        return [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
        ];
    }


    /**
     * @Route("/privacy", name="privacy")
     * @Template()
     */
    public function privacyAction(Request $request) {

    }
}
