<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\LocationCategory;
use App\Form\LocationCategoryType;
use App\Repository\LocationCategoryRepository;

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
 * @Route("/location_category")
 */
class LocationCategoryController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="location_category_index", methods={"GET"})
     *
     * @Template
     */
    public function index(Request $request, LocationCategoryRepository $locationCategoryRepository) : array {
        $query = $locationCategoryRepository->indexQuery();
        $pageSize = (int) $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'location_categories' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/search", name="location_category_search", methods={"GET"})
     *
     * @Template
     *
     * @return array
     */
    public function search(Request $request, LocationCategoryRepository $locationCategoryRepository) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $locationCategoryRepository->searchQuery($q);
            $locationCategories = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);
        } else {
            $locationCategories = [];
        }

        return [
            'location_categories' => $locationCategories,
            'q' => $q,
        ];
    }

    /**
     * @Route("/typeahead", name="location_category_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, LocationCategoryRepository $locationCategoryRepository) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];
        foreach ($locationCategoryRepository->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="location_category_new", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $locationCategory = new LocationCategory();
        $form = $this->createForm(LocationCategoryType::class, $locationCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($locationCategory);
            $entityManager->flush();
            $this->addFlash('success', 'The new locationCategory has been saved.');

            return $this->redirectToRoute('location_category_show', ['id' => $locationCategory->getId()]);
        }

        return [
            'location_category' => $locationCategory,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new_popup", name="location_category_new_popup", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request) {
        return $this->new($request);
    }

    /**
     * @Route("/{id}", name="location_category_show", methods={"GET"})
     * @Template
     *
     * @return array
     */
    public function show(LocationCategory $locationCategory) {
        return [
            'location_category' => $locationCategory,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="location_category_edit", methods={"GET", "POST"})
     *
     * @Template
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, LocationCategory $locationCategory) {
        $form = $this->createForm(LocationCategoryType::class, $locationCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated locationCategory has been saved.');

            return $this->redirectToRoute('location_category_show', ['id' => $locationCategory->getId()]);
        }

        return [
            'location_category' => $locationCategory,
            'form' => $form->createView(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="location_category_delete", methods={"DELETE"})
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, LocationCategory $locationCategory) {
        if ($this->isCsrfTokenValid('delete' . $locationCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($locationCategory);
            $entityManager->flush();
            $this->addFlash('success', 'The locationCategory has been deleted.');
        }

        return $this->redirectToRoute('location_category_index');
    }
}
