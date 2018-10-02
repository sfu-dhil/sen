<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\TransactionCategory;
use AppBundle\Form\TransactionCategoryType;

/**
 * TransactionCategory controller.
 *
 * @Security("has_role('ROLE_USER')")
 * @Route("/transaction_category")
 */
class TransactionCategoryController extends Controller
{
    /**
     * Lists all TransactionCategory entities.
     *
     * @param Request $request
     *
     * @return array
     *
     * @Route("/", name="transaction_category_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(TransactionCategory::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $transactionCategories = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'transactionCategories' => $transactionCategories,
        );
    }

/**
     * Typeahead API endpoint for TransactionCategory entities.
     *
     * To make this work, add something like this to TransactionCategoryRepository:
        //    public function typeaheadQuery($q) {
        //        $qb = $this->createQueryBuilder('e');
        //        $qb->andWhere("e.name LIKE :q");
        //        $qb->orderBy('e.name');
        //        $qb->setParameter('q', "{$q}%");
        //        return $qb->getQuery()->execute();
        //    }
     *
     * @param Request $request
     *
     * @Route("/typeahead", name="transaction_category_typeahead")
     * @Method("GET")
     * @return JsonResponse
     */
    public function typeahead(Request $request)
    {
        $q = $request->query->get('q');
        if( ! $q) {
            return new JsonResponse([]);
        }
        $em = $this->getDoctrine()->getManager();
	$repo = $em->getRepository(TransactionCategory::class);
        $data = [];
        foreach($repo->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string)$result,
            ];
        }
        return new JsonResponse($data);
    }
    /**
     * Search for TransactionCategory entities.
     *
     * To make this work, add a method like this one to the
     * AppBundle:TransactionCategory repository. Replace the fieldName with
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
     * @Route("/search", name="transaction_category_search")
     * @Method("GET")
     * @Template()
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
	$repo = $em->getRepository('AppBundle:TransactionCategory');
	$q = $request->query->get('q');
	if($q) {
	    $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $transactionCategories = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
	} else {
            $transactionCategories = array();
	}

        return array(
            'transactionCategories' => $transactionCategories,
            'q' => $q,
        );
    }

    /**
     * Creates a new TransactionCategory entity.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="transaction_category_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request)
    {
        $transactionCategory = new TransactionCategory();
        $form = $this->createForm(TransactionCategoryType::class, $transactionCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($transactionCategory);
            $em->flush();

            $this->addFlash('success', 'The new transactionCategory was created.');
            return $this->redirectToRoute('transaction_category_show', array('id' => $transactionCategory->getId()));
        }

        return array(
            'transactionCategory' => $transactionCategory,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new TransactionCategory entity in a popup.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="transaction_category_new_popup")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newPopupAction(Request $request)
    {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a TransactionCategory entity.
     *
     * @param TransactionCategory $transactionCategory
     *
     * @return array
     *
     * @Route("/{id}", name="transaction_category_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(TransactionCategory $transactionCategory)
    {

        return array(
            'transactionCategory' => $transactionCategory,
        );
    }

    /**
     * Displays a form to edit an existing TransactionCategory entity.
     *
     *
     * @param Request $request
     * @param TransactionCategory $transactionCategory
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="transaction_category_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction(Request $request, TransactionCategory $transactionCategory)
    {
        $editForm = $this->createForm(TransactionCategoryType::class, $transactionCategory);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The transactionCategory has been updated.');
            return $this->redirectToRoute('transaction_category_show', array('id' => $transactionCategory->getId()));
        }

        return array(
            'transactionCategory' => $transactionCategory,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a TransactionCategory entity.
     *
     *
     * @param Request $request
     * @param TransactionCategory $transactionCategory
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="transaction_category_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, TransactionCategory $transactionCategory)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($transactionCategory);
        $em->flush();
        $this->addFlash('success', 'The transactionCategory was deleted.');

        return $this->redirectToRoute('transaction_category_index');
    }
}
