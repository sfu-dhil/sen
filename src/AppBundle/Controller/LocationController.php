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
use AppBundle\Entity\Location;
use AppBundle\Form\LocationType;

/**
 * Location controller.
 *
 * @Route("/location")
 */
class LocationController extends Controller {

    /**
     * Lists all Location entities.
     *
     * @param Request $request
     *
     * @return array
     *
     * @Route("/", name="location_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Location::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $locations = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'locations' => $locations,
        );
    }

    /**
     * Typeahead API endpoint for Location entities.
     *
     * To make this work, add something like this to LocationRepository:
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
     * @Route("/typeahead", name="location_typeahead")
     * @Method("GET")
     * @return JsonResponse
     */
    public function typeahead(Request $request) {
        $q = $request->query->get('q');
        if (!$q) {
            return new JsonResponse([]);
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Location::class);
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
     * Search for Location entities.
     *
     * To make this work, add a method like this one to the
     * AppBundle:Location repository. Replace the fieldName with
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
     * @Route("/search", name="location_search")
     * @Method("GET")
     * @Template()
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Location');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $locations = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $locations = array();
        }

        return array(
            'locations' => $locations,
            'q' => $q,
        );
    }

    /**
     * Creates a new Location entity.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="location_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request) {
        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($location);
            $em->flush();

            $this->addFlash('success', 'The new location was created.');
            return $this->redirectToRoute('location_show', array('id' => $location->getId()));
        }

        return array(
            'location' => $location,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Location entity in a popup.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="location_new_popup")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newPopupAction(Request $request) {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a Location entity.
     *
     * @param Location $location
     *
     * @return array
     *
     * @Route("/{id}", name="location_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Location $location) {

        return array(
            'location' => $location,
        );
    }

    /**
     * Displays a form to edit an existing Location entity.
     *
     *
     * @param Request $request
     * @param Location $location
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="location_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction(Request $request, Location $location) {
        $editForm = $this->createForm(LocationType::class, $location);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The location has been updated.');
            return $this->redirectToRoute('location_show', array('id' => $location->getId()));
        }

        return array(
            'location' => $location,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Location entity.
     *
     *
     * @param Request $request
     * @param Location $location
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="location_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, Location $location) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($location);
        $em->flush();
        $this->addFlash('success', 'The location was deleted.');

        return $this->redirectToRoute('location_index');
    }

}
