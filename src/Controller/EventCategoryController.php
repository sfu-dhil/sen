<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\EventCategory;
use App\Form\EventCategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * EventCategory controller.
 *
 * @Route("/event_category")
 */
class EventCategoryController extends AbstractController {
    /**
     * Lists all EventCategory entities.
     *
     * @return array
     *
     * @Route("/", name="event_category_index", methods={"GET"})
     *
     * @Template()
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(EventCategory::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();

        $eventCategories = $this->paginator->paginate($query, $request->query->getint('page', 1), 25);

        return [
            'eventCategories' => $eventCategories,
        ];
    }

    /**
     * Typeahead API endpoint for EventCategory entities.
     *
     * To make this work, add something like this to EventCategoryRepository:
     *
     * @Route("/typeahead", name="event_category_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request) {
        $q = $request->query->get('q');
        if ( ! $q) {
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
     * App:EventCategory repository. Replace the fieldName with
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
     * @Route("/search", name="event_category_search", methods={"GET"})
     *
     * @Template()
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('App:EventCategory');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);

            $eventCategories = $this->paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $eventCategories = [];
        }

        return [
            'eventCategories' => $eventCategories,
            'q' => $q,
        ];
    }

    /**
     * Creates a new EventCategory entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="event_category_new", methods={"GET","POST"})
     *
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

            return $this->redirectToRoute('event_category_show', ['id' => $eventCategory->getId()]);
        }

        return [
            'eventCategory' => $eventCategory,
            'form' => $form->createView(),
        ];
    }

    /**
     * Creates a new EventCategory entity in a popup.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="event_category_new_popup", methods={"GET","POST"})
     *
     * @Template()
     */
    public function newPopupAction(Request $request) {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a EventCategory entity.
     *
     * @return array
     *
     * @Route("/{id}", name="event_category_show", methods={"GET"})
     *
     * @Template()
     */
    public function showAction(EventCategory $eventCategory) {
        return [
            'eventCategory' => $eventCategory,
        ];
    }

    /**
     * Displays a form to edit an existing EventCategory entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="event_category_edit", methods={"GET","POST"})
     *
     * @Template()
     */
    public function editAction(Request $request, EventCategory $eventCategory) {
        $editForm = $this->createForm(EventCategoryType::class, $eventCategory);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The eventCategory has been updated.');

            return $this->redirectToRoute('event_category_show', ['id' => $eventCategory->getId()]);
        }

        return [
            'eventCategory' => $eventCategory,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Deletes a EventCategory entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="event_category_delete", methods={"GET"})
     */
    public function deleteAction(Request $request, EventCategory $eventCategory) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($eventCategory);
        $em->flush();
        $this->addFlash('success', 'The eventCategory was deleted.');

        return $this->redirectToRoute('event_category_index');
    }
}
