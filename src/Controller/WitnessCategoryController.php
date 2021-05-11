<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\WitnessCategory;
use App\Form\WitnessCategoryType;
use App\Repository\WitnessCategoryRepository;

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
 * @Route("/witness_category")
 */
class WitnessCategoryController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="witness_category_index", methods={"GET"})
     *
     * @Template
     */
    public function index(Request $request, WitnessCategoryRepository $witnessCategoryRepository) : array {
        $query = $witnessCategoryRepository->indexQuery();
        $pageSize = (int) $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'witness_categories' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/search", name="witness_category_search", methods={"GET"})
     *
     * @Template
     *
     * @return array
     */
    public function search(Request $request, WitnessCategoryRepository $witnessCategoryRepository) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $witnessCategoryRepository->searchQuery($q);
            $witnessCategories = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);
        } else {
            $witnessCategories = [];
        }

        return [
            'witness_categories' => $witnessCategories,
            'q' => $q,
        ];
    }

    /**
     * @Route("/typeahead", name="witness_category_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, WitnessCategoryRepository $witnessCategoryRepository) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];
        foreach ($witnessCategoryRepository->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="witness_category_new", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $witnessCategory = new WitnessCategory();
        $form = $this->createForm(WitnessCategoryType::class, $witnessCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($witnessCategory);
            $entityManager->flush();
            $this->addFlash('success', 'The new witnessCategory has been saved.');

            return $this->redirectToRoute('witness_category_show', ['id' => $witnessCategory->getId()]);
        }

        return [
            'witness_category' => $witnessCategory,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new_popup", name="witness_category_new_popup", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request) {
        return $this->new($request);
    }

    /**
     * @Route("/{id}", name="witness_category_show", methods={"GET"})
     * @Template
     *
     * @return array
     */
    public function show(WitnessCategory $witnessCategory) {
        return [
            'witness_category' => $witnessCategory,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="witness_category_edit", methods={"GET", "POST"})
     *
     * @Template
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, WitnessCategory $witnessCategory) {
        $form = $this->createForm(WitnessCategoryType::class, $witnessCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated witnessCategory has been saved.');

            return $this->redirectToRoute('witness_category_show', ['id' => $witnessCategory->getId()]);
        }

        return [
            'witness_category' => $witnessCategory,
            'form' => $form->createView(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="witness_category_delete", methods={"DELETE"})
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, WitnessCategory $witnessCategory) {
        if ($this->isCsrfTokenValid('delete' . $witnessCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($witnessCategory);
            $entityManager->flush();
            $this->addFlash('success', 'The witnessCategory has been deleted.');
        }

        return $this->redirectToRoute('witness_category_index');
    }
}
