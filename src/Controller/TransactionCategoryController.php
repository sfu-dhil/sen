<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\TransactionCategory;
use App\Form\TransactionCategoryType;
use App\Repository\TransactionCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * TransactionCategory controller.
 *
 * @Route("/transaction_category")
 */
class TransactionCategoryController extends AbstractController implements PaginatorAwareInterface
{
    use PaginatorTrait;

    /**
     * Lists all TransactionCategory entities.
     *
     * @return array
     *
     * @Route("/", name="transaction_category_index", methods={"GET"})
     *
     * @Template
     */
    public function indexAction(Request $request, EntityManagerInterface $em) {
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(TransactionCategory::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();

        $transactionCategories = $this->paginator->paginate($query, $request->query->getint('page', 1), 25);

        return [
            'transactionCategories' => $transactionCategories,
        ];
    }

    /**
     * Typeahead API endpoint for TransactionCategory entities.
     *
     * To make this work, add something like this to TransactionCategoryRepository:
     *
     * @Route("/typeahead", name="transaction_category_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, TransactionCategoryRepository $repo) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }

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
     * Search for TransactionCategory entities.
     *
     * To make this work, add a method like this one to the
     * App:TransactionCategory repository. Replace the fieldName with
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
     * @Route("/search", name="transaction_category_search", methods={"GET"})
     *
     * @Template
     */
    public function searchAction(Request $request, TransactionCategoryRepository $repo) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);

            $transactionCategories = $this->paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $transactionCategories = [];
        }

        return [
            'transactionCategories' => $transactionCategories,
            'q' => $q,
        ];
    }

    /**
     * Creates a new TransactionCategory entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="transaction_category_new", methods={"GET", "POST"})
     *
     * @Template
     */
    public function newAction(Request $request, EntityManagerInterface $em) {
        $transactionCategory = new TransactionCategory();
        $form = $this->createForm(TransactionCategoryType::class, $transactionCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($transactionCategory);
            $em->flush();

            $this->addFlash('success', 'The new transactionCategory was created.');

            return $this->redirectToRoute('transaction_category_show', ['id' => $transactionCategory->getId()]);
        }

        return [
            'transactionCategory' => $transactionCategory,
            'form' => $form->createView(),
        ];
    }

    /**
     * Creates a new TransactionCategory entity in a popup.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="transaction_category_new_popup", methods={"GET", "POST"})
     *
     * @Template
     */
    public function newPopupAction(Request $request, EntityManagerInterface $em) {
        return $this->newAction($request, $em);
    }

    /**
     * Finds and displays a TransactionCategory entity.
     *
     * @return array
     *
     * @Route("/{id}", name="transaction_category_show", methods={"GET"})
     *
     * @Template
     */
    public function showAction(TransactionCategory $transactionCategory) {
        return [
            'transactionCategory' => $transactionCategory,
        ];
    }

    /**
     * Displays a form to edit an existing TransactionCategory entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="transaction_category_edit", methods={"GET", "POST"})
     *
     * @Template
     */
    public function editAction(Request $request, EntityManagerInterface $em, TransactionCategory $transactionCategory) {
        $editForm = $this->createForm(TransactionCategoryType::class, $transactionCategory);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The transactionCategory has been updated.');

            return $this->redirectToRoute('transaction_category_show', ['id' => $transactionCategory->getId()]);
        }

        return [
            'transactionCategory' => $transactionCategory,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Deletes a TransactionCategory entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="transaction_category_delete", methods={"GET"})
     */
    public function deleteAction(Request $request, EntityManagerInterface $em, TransactionCategory $transactionCategory) {
        $em->remove($transactionCategory);
        $em->flush();
        $this->addFlash('success', 'The transactionCategory was deleted.');

        return $this->redirectToRoute('transaction_category_index');
    }
}
