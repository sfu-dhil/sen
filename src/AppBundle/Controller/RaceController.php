<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Race;
use AppBundle\Form\RaceType;

/**
 * Race controller.
 *
 * @Route("/race")
 */
class RaceController extends Controller {

    /**
     * Lists all Race entities.
     *
     * @param Request $request
     *
     * @return array
     *
     * @Route("/", name="race_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Race::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $races = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'races' => $races,
        );
    }

    /**
     * Typeahead API endpoint for Race entities.
     *
     * To make this work, add something like this to RaceRepository:
      //    public function typeaheadQuery($q) {
      //        $qb = $this->createQueryBuilder('e');
      //        $qb->andWhere("e.name LIKE :q");
      //        $qb->orderBy('e.name');
      //        $qb->setParameter('q', "{$q}%");
      //        return $qb->getQuery()->execute();
      //    }
     *
     * @param Request $request
     *
     * @Route("/typeahead", name="race_typeahead")
     * @Method("GET")
     * @return JsonResponse
     */
    public function typeahead(Request $request) {
        $q = $request->query->get('q');
        if (!$q) {
            return new JsonResponse([]);
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Race::class);
        $data = [];
        foreach ($repo->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }
        return new JsonResponse($data);
    }

    /**
     * Search for Race entities.
     *
     * To make this work, add a method like this one to the
     * AppBundle:Race repository. Replace the fieldName with
     * something appropriate, and adjust the generated search.html.twig
     * template.
     *
     * <code><pre>
     *    public function searchQuery($q) {
     *       $qb = $this->createQueryBuilder('e');
     *       $qb->addSelect("MATCH (e.title) AGAINST(:q BOOLEAN) as HIDDEN score");
     *       $qb->orderBy('score', 'DESC');
     *       $qb->setParameter('q', $q);
     *       return $qb->getQuery();
     *    }
     * </pre></code>
     *
     * @param Request $request
     *
     * @Route("/search", name="race_search")
     * @Method("GET")
     * @Template()
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Race');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $races = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $races = array();
        }

        return array(
            'races' => $races,
            'q' => $q,
        );
    }

    /**
     * Creates a new Race entity.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="race_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request) {
        $race = new Race();
        $form = $this->createForm(RaceType::class, $race);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($race);
            $em->flush();

            $this->addFlash('success', 'The new race was created.');
            return $this->redirectToRoute('race_show', array('id' => $race->getId()));
        }

        return array(
            'race' => $race,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Race entity in a popup.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="race_new_popup")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newPopupAction(Request $request) {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a Race entity.
     *
     * @param Race $race
     *
     * @return array
     *
     * @Route("/{id}", name="race_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Race $race) {

        return array(
            'race' => $race,
        );
    }

    /**
     * Displays a form to edit an existing Race entity.
     *
     *
     * @param Request $request
     * @param Race $race
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="race_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction(Request $request, Race $race) {
        $editForm = $this->createForm(RaceType::class, $race);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The race has been updated.');
            return $this->redirectToRoute('race_show', array('id' => $race->getId()));
        }

        return array(
            'race' => $race,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Race entity.
     *
     *
     * @param Request $request
     * @param Race $race
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="race_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, Race $race) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($race);
        $em->flush();
        $this->addFlash('success', 'The race was deleted.');

        return $this->redirectToRoute('race_index');
    }

}
