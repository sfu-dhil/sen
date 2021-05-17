<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\RelationshipCategory;
use App\Form\RelationshipCategoryType;
use App\Repository\RelationshipCategoryRepository;

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
 * @Route("/relationship_category")
 */
class RelationshipCategoryController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="relationship_category_index", methods={"GET"})
     *
     * @Template
     */
    public function index(Request $request, RelationshipCategoryRepository $relationshipCategoryRepository) : array {
        $query = $relationshipCategoryRepository->indexQuery();
        $pageSize = (int) $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'relationship_categories' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/search", name="relationship_category_search", methods={"GET"})
     *
     * @Template
     *
     * @return array
     */
    public function search(Request $request, RelationshipCategoryRepository $relationshipCategoryRepository) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $relationshipCategoryRepository->searchQuery($q);
            $relationshipCategories = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);
        } else {
            $relationshipCategories = [];
        }

        return [
            'relationship_categories' => $relationshipCategories,
            'q' => $q,
        ];
    }

    /**
     * @Route("/typeahead", name="relationship_category_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, RelationshipCategoryRepository $relationshipCategoryRepository) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];
        foreach ($relationshipCategoryRepository->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="relationship_category_new", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $relationshipCategory = new RelationshipCategory();
        $form = $this->createForm(RelationshipCategoryType::class, $relationshipCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($relationshipCategory);
            $entityManager->flush();
            $this->addFlash('success', 'The new relationshipCategory has been saved.');

            return $this->redirectToRoute('relationship_category_show', ['id' => $relationshipCategory->getId()]);
        }

        return [
            'relationship_category' => $relationshipCategory,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new_popup", name="relationship_category_new_popup", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request) {
        return $this->new($request);
    }

    /**
     * @Route("/{id}", name="relationship_category_show", methods={"GET"})
     * @Template
     *
     * @return array
     */
    public function show(RelationshipCategory $relationshipCategory) {
        return [
            'relationship_category' => $relationshipCategory,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="relationship_category_edit", methods={"GET", "POST"})
     *
     * @Template
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, RelationshipCategory $relationshipCategory) {
        $form = $this->createForm(RelationshipCategoryType::class, $relationshipCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated relationshipCategory has been saved.');

            return $this->redirectToRoute('relationship_category_show', ['id' => $relationshipCategory->getId()]);
        }

        return [
            'relationship_category' => $relationshipCategory,
            'form' => $form->createView(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="relationship_category_delete", methods={"DELETE"})
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, RelationshipCategory $relationshipCategory) {
        if ($this->isCsrfTokenValid('delete' . $relationshipCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($relationshipCategory);
            $entityManager->flush();
            $this->addFlash('success', 'The relationshipCategory has been deleted.');
        }

        return $this->redirectToRoute('relationship_category_index');
    }
}
