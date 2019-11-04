<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Transaction;
use AppBundle\Form\TransactionType;

/**
 * Transaction controller.
 *
 * @Route("/transaction")
 */
class TransactionController extends Controller {

    /**
     * Lists all Transaction entities.
     *
     * @param Request $request
     *
     * @return array
     *
     * @Route("/", name="transaction_index", methods={"GET"})
     *
     * @Template()
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Transaction::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $transactions = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'transactions' => $transactions,
        );
    }

    /**
     * Search for Transaction entities.
     *
     * To make this work, add a method like this one to the
     * AppBundle:Transaction repository. Replace the fieldName with
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
     * @Route("/search", name="transaction_search", methods={"GET"})
     *
     * @Template()
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Transaction');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $transactions = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $transactions = array();
        }

        return array(
            'transactions' => $transactions,
            'q' => $q,
        );
    }

    /**
     * Creates a new Transaction entity.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="transaction_new", methods={"GET","POST"})
     *
     * @Template()
     */
    public function newAction(Request $request) {
        $transaction = new Transaction();
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($transaction);
            $em->flush();

            $this->addFlash('success', 'The new transaction was created.');
            return $this->redirectToRoute('transaction_show', array('id' => $transaction->getId()));
        }

        return array(
            'transaction' => $transaction,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Transaction entity in a popup.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="transaction_new_popup", methods={"GET","POST"})
     *
     * @Template()
     */
    public function newPopupAction(Request $request) {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a Transaction entity.
     *
     * @param Transaction $transaction
     *
     * @return array
     *
     * @Route("/{id}", name="transaction_show", methods={"GET"})
     *
     * @Template()
     */
    public function showAction(Transaction $transaction) {

        return array(
            'transaction' => $transaction,
        );
    }

    /**
     * Displays a form to edit an existing Transaction entity.
     *
     *
     * @param Request $request
     * @param Transaction $transaction
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="transaction_edit", methods={"GET","POST"})
     *
     * @Template()
     */
    public function editAction(Request $request, Transaction $transaction) {
        $editForm = $this->createForm(TransactionType::class, $transaction);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The transaction has been updated.');
            return $this->redirectToRoute('transaction_show', array('id' => $transaction->getId()));
        }

        return array(
            'transaction' => $transaction,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Transaction entity.
     *
     *
     * @param Request $request
     * @param Transaction $transaction
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="transaction_delete", methods={"GET"})
     *
     */
    public function deleteAction(Request $request, Transaction $transaction) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($transaction);
        $em->flush();
        $this->addFlash('success', 'The transaction was deleted.');

        return $this->redirectToRoute('transaction_index');
    }

}
