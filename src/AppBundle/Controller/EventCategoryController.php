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
use AppBundle\Entity\EventCategory;
use AppBundle\Form\EventCategoryType;

/**
 * EventCategory controller.
 *
 * @Route("/event_category")
 */
class EventCategoryController extends Controller {

    /**
     * Lists all EventCategory entities.
     *
     * @param Request $request
     *
     * @return array
     *
     * @Route("/", name="event_category_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(EventCategory::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $eventCategories = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'eventCategories' => $eventCategories,
        );
    }

    /**
     * Typeahead API endpoint for EventCategory entities.
     *
     * To make this work, add something like this to EventCategoryRepository:
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
     * @Route("/typeahead", name="event_category_typeahead")
     * @Method("GET")
     * @return JsonResponse
     */
    public function typeahead(Request $request) {
        $q = $request->query->get('q');
        if (!$q) {
            return new JsonResponse([]);
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(EventCategory::class);
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
     * Search for EventCategory entities.
     *
     * To make this work, add a method like this one to the
     * AppBundle:EventCategory repository. Replace the fieldName with
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
     * @Route("/search", name="event_category_search")
     * @Method("GET")
     * @Template()
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:EventCategory');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $eventCategories = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $eventCategories = array();
        }

        return array(
            'eventCategories' => $eventCategories,
            'q' => $q,
        );
    }

    /**
     * Creates a new EventCategory entity.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="event_category_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request) {
        $eventCategory = new EventCategory();
        $form = $this->createForm(EventCategoryType::class, $eventCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($eventCategory);
            $em->flush();

            $this->addFlash('success', 'The new eventCategory was created.');
            return $this->redirectToRoute('event_category_show', array('id' => $eventCategory->getId()));
        }

        return array(
            'eventCategory' => $eventCategory,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new EventCategory entity in a popup.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="event_category_new_popup")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newPopupAction(Request $request) {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a EventCategory entity.
     *
     * @param EventCategory $eventCategory
     *
     * @return array
     *
     * @Route("/{id}", name="event_category_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(EventCategory $eventCategory) {

        return array(
            'eventCategory' => $eventCategory,
        );
    }

    /**
     * Displays a form to edit an existing EventCategory entity.
     *
     *
     * @param Request $request
     * @param EventCategory $eventCategory
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="event_category_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction(Request $request, EventCategory $eventCategory) {
        $editForm = $this->createForm(EventCategoryType::class, $eventCategory);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The eventCategory has been updated.');
            return $this->redirectToRoute('event_category_show', array('id' => $eventCategory->getId()));
        }

        return array(
            'eventCategory' => $eventCategory,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a EventCategory entity.
     *
     *
     * @param Request $request
     * @param EventCategory $eventCategory
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="event_category_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, EventCategory $eventCategory) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($eventCategory);
        $em->flush();
        $this->addFlash('success', 'The eventCategory was deleted.');

        return $this->redirectToRoute('event_category_index');
    }

}
