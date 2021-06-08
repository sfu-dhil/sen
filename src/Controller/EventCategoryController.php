<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\EventCategory;
use App\Form\EventCategoryType;
use App\Repository\EventCategoryRepository;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/event_category")
 */
class EventCategoryController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="event_category_index", methods={"GET"})
     *
     * @Template
     */
    public function index(Request $request, EventCategoryRepository $eventCategoryRepository) : array {
        $query = $eventCategoryRepository->indexQuery();
        $pageSize = (int) $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'event_categories' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/search", name="event_category_search", methods={"GET"})
     *
     * @Template
     */
    public function search(Request $request, EventCategoryRepository $eventCategoryRepository) : array {
        $q = $request->query->get('q');
        if ($q) {
            $query = $eventCategoryRepository->searchQuery($q);
            $eventCategories = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);
        } else {
            $eventCategories = [];
        }

        return [
            'event_categories' => $eventCategories,
            'q' => $q,
        ];
    }

    /**
     * @Route("/typeahead", name="event_category_typeahead", methods={"GET"})
     */
    public function typeahead(Request $request, EventCategoryRepository $eventCategoryRepository) : JsonResponse {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];
        foreach ($eventCategoryRepository->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="event_category_new", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $eventCategory = new EventCategory();
        $form = $this->createForm(EventCategoryType::class, $eventCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($eventCategory);
            $entityManager->flush();
            $this->addFlash('success', 'The new eventCategory has been saved.');

            return $this->redirectToRoute('event_category_show', ['id' => $eventCategory->getId()]);
        }

        return [
            'event_category' => $eventCategory,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new_popup", name="event_category_new_popup", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request) {
        return $this->new($request);
    }

    /**
     * @Route("/{id}", name="event_category_show", methods={"GET"})
     * @Template
     */
    public function show(EventCategory $eventCategory) : array {
        return [
            'event_category' => $eventCategory,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="event_category_edit", methods={"GET", "POST"})
     *
     * @Template
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, EventCategory $eventCategory) {
        $form = $this->createForm(EventCategoryType::class, $eventCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated eventCategory has been saved.');

            return $this->redirectToRoute('event_category_show', ['id' => $eventCategory->getId()]);
        }

        return [
            'event_category' => $eventCategory,
            'form' => $form->createView(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="event_category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EventCategory $eventCategory) : RedirectResponse {
        if ($this->isCsrfTokenValid('delete' . $eventCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eventCategory);
            $entityManager->flush();
            $this->addFlash('success', 'The eventCategory has been deleted.');
        }

        return $this->redirectToRoute('event_category_index');
    }
}
