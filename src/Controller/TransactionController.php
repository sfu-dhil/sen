<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Transaction;
use App\Form\TransactionType;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Transaction controller.
 *
 * @Route("/transaction")
 */
class TransactionController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Lists all Transaction entities.
     *
     * @return array
     *
     * @Route("/", name="transaction_index", methods={"GET"})
     *
     * @Template()
     */
    public function indexAction(Request $request, EntityManagerInterface $em) {
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Transaction::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();

        $transactions = $this->paginator->paginate($query, $request->query->getint('page', 1), 25);

        return [
            'transactions' => $transactions,
        ];
    }

    /**
     * Search for Transaction entities.
     *
     * To make this work, add a method like this one to the
     * App:Transaction repository. Replace the fieldName with
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
     * @Route("/search", name="transaction_search", methods={"GET"})
     *
     * @Template()
     */
    public function searchAction(Request $request, TransactionRepository $repo) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);

            $transactions = $this->paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $transactions = [];
        }

        return [
            'transactions' => $transactions,
            'q' => $q,
        ];
    }

    /**
     * Creates a new Transaction entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="transaction_new", methods={"GET","POST"})
     *
     * @Template()
     */
    public function newAction(Request $request, EntityManagerInterface $em) {
        $transaction = new Transaction();
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($transaction);
            $em->flush();

            $this->addFlash('success', 'The new transaction was created.');

            return $this->redirectToRoute('transaction_show', ['id' => $transaction->getId()]);
        }

        return [
            'transaction' => $transaction,
            'form' => $form->createView(),
        ];
    }

    /**
     * Creates a new Transaction entity in a popup.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="transaction_new_popup", methods={"GET","POST"})
     *
     * @Template()
     */
    public function newPopupAction(Request $request, EntityManagerInterface $em) {
        return $this->newAction($request, $em);
    }

    /**
     * Finds and displays a Transaction entity.
     *
     * @return array
     *
     * @Route("/{id}", name="transaction_show", methods={"GET"})
     *
     * @Template()
     */
    public function showAction(Transaction $transaction) {
        return [
            'transaction' => $transaction,
        ];
    }

    /**
     * Displays a form to edit an existing Transaction entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="transaction_edit", methods={"GET","POST"})
     *
     * @Template()
     */
    public function editAction(Request $request, EntityManagerInterface $em, Transaction $transaction) {
        $editForm = $this->createForm(TransactionType::class, $transaction);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The transaction has been updated.');

            return $this->redirectToRoute('transaction_show', ['id' => $transaction->getId()]);
        }

        return [
            'transaction' => $transaction,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Deletes a Transaction entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="transaction_delete", methods={"GET"})
     */
    public function deleteAction(Request $request, EntityManagerInterface $em, Transaction $transaction) {
        $em->remove($transaction);
        $em->flush();
        $this->addFlash('success', 'The transaction was deleted.');

        return $this->redirectToRoute('transaction_index');
    }
}
