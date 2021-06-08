<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\TransactionCategory;
use App\Form\TransactionCategoryType;
use App\Repository\TransactionCategoryRepository;
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
 * @Route("/transaction_category")
 */
class TransactionCategoryController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="transaction_category_index", methods={"GET"})
     *
     * @Template
     */
    public function index(Request $request, TransactionCategoryRepository $transactionCategoryRepository) : array {
        $query = $transactionCategoryRepository->indexQuery();
        $pageSize = (int) $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'transaction_categories' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/search", name="transaction_category_search", methods={"GET"})
     *
     * @Template
     */
    public function search(Request $request, TransactionCategoryRepository $transactionCategoryRepository) : array {
        $q = $request->query->get('q');
        if ($q) {
            $query = $transactionCategoryRepository->searchQuery($q);
            $transactionCategories = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);
        } else {
            $transactionCategories = [];
        }

        return [
            'transaction_categories' => $transactionCategories,
            'q' => $q,
        ];
    }

    /**
     * @Route("/typeahead", name="transaction_category_typeahead", methods={"GET"})
     */
    public function typeahead(Request $request, TransactionCategoryRepository $transactionCategoryRepository) : JsonResponse {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];
        foreach ($transactionCategoryRepository->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="transaction_category_new", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $transactionCategory = new TransactionCategory();
        $form = $this->createForm(TransactionCategoryType::class, $transactionCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($transactionCategory);
            $entityManager->flush();
            $this->addFlash('success', 'The new transactionCategory has been saved.');

            return $this->redirectToRoute('transaction_category_show', ['id' => $transactionCategory->getId()]);
        }

        return [
            'transaction_category' => $transactionCategory,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new_popup", name="transaction_category_new_popup", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request) {
        return $this->new($request);
    }

    /**
     * @Route("/{id}", name="transaction_category_show", methods={"GET"})
     * @Template
     */
    public function show(TransactionCategory $transactionCategory) : array {
        return [
            'transaction_category' => $transactionCategory,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="transaction_category_edit", methods={"GET", "POST"})
     *
     * @Template
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, TransactionCategory $transactionCategory) {
        $form = $this->createForm(TransactionCategoryType::class, $transactionCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated transactionCategory has been saved.');

            return $this->redirectToRoute('transaction_category_show', ['id' => $transactionCategory->getId()]);
        }

        return [
            'transaction_category' => $transactionCategory,
            'form' => $form->createView(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="transaction_category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TransactionCategory $transactionCategory) : RedirectResponse {
        if ($this->isCsrfTokenValid('delete' . $transactionCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($transactionCategory);
            $entityManager->flush();
            $this->addFlash('success', 'The transactionCategory has been deleted.');
        }

        return $this->redirectToRoute('transaction_category_index');
    }
}
