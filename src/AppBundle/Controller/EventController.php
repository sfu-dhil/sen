<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Event;
use AppBundle\Form\EventType;

/**
 * Event controller.
 *
 * @Route("/event")
 */
class EventController extends Controller {

    /**
     * Lists all Event entities.
     *
     * @param Request $request
     *
     * @return array
     *
     * @Route("/", name="event_index", methods={"GET"})
     *
     * @Template()
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Event::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $events = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'events' => $events,
        );
    }

    /**
     * Search for Event entities.
     *
     * To make this work, add a method like this one to the
     * AppBundle:Event repository. Replace the fieldName with
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
     * @Route("/search", name="event_search", methods={"GET"})
     *
     * @Template()
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Event');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $events = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $events = array();
        }

        return array(
            'events' => $events,
            'q' => $q,
        );
    }

    /**
     * Creates a new Event entity.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="event_new", methods={"GET","POST"})
     *
     * @Template()
     */
    public function newAction(Request $request) {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            $this->addFlash('success', 'The new event was created.');
            return $this->redirectToRoute('event_show', array('id' => $event->getId()));
        }

        return array(
            'event' => $event,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Event entity in a popup.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="event_new_popup", methods={"GET","POST"})
     *
     * @Template()
     */
    public function newPopupAction(Request $request) {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a Event entity.
     *
     * @param Event $event
     *
     * @return array
     *
     * @Route("/{id}", name="event_show", methods={"GET"})
     *
     * @Template()
     */
    public function showAction(Event $event) {

        return array(
            'event' => $event,
        );
    }

    /**
     * Displays a form to edit an existing Event entity.
     *
     *
     * @param Request $request
     * @param Event $event
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="event_edit", methods={"GET","POST"})
     *
     * @Template()
     */
    public function editAction(Request $request, Event $event) {
        $editForm = $this->createForm(EventType::class, $event);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The event has been updated.');
            return $this->redirectToRoute('event_show', array('id' => $event->getId()));
        }

        return array(
            'event' => $event,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Event entity.
     *
     *
     * @param Request $request
     * @param Event $event
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="event_delete", methods={"GET"})
     *
     */
    public function deleteAction(Request $request, Event $event) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($event);
        $em->flush();
        $this->addFlash('success', 'The event was deleted.');

        return $this->redirectToRoute('event_index');
    }

}
