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
use AppBundle\Entity\Ledger;
use AppBundle\Form\LedgerType;

/**
 * Ledger controller.
 *
 * @Route("/ledger")
 */
class LedgerController extends Controller {

    /**
     * Lists all Ledger entities.
     *
     * @param Request $request
     *
     * @return array
     *
     * @Route("/", name="ledger_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Ledger::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $ledgers = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'ledgers' => $ledgers,
        );
    }

    /**
     * Typeahead API endpoint for Ledger entities.
     *
     * To make this work, add something like this to LedgerRepository:
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
     * @Route("/typeahead", name="ledger_typeahead")
     * @Method("GET")
     * @return JsonResponse
     */
    public function typeahead(Request $request) {
        $q = $request->query->get('q');
        if (!$q) {
            return new JsonResponse([]);
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Ledger::class);
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
     * Search for Ledger entities.
     *
     * To make this work, add a method like this one to the
     * AppBundle:Ledger repository. Replace the fieldName with
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
     * @Route("/search", name="ledger_search")
     * @Method("GET")
     * @Template()
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Ledger');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $ledgers = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $ledgers = array();
        }

        return array(
            'ledgers' => $ledgers,
            'q' => $q,
        );
    }

    /**
     * Creates a new Ledger entity.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="ledger_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request) {
        $ledger = new Ledger();
        $form = $this->createForm(LedgerType::class, $ledger);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ledger);
            $em->flush();

            $this->addFlash('success', 'The new ledger was created.');
            return $this->redirectToRoute('ledger_show', array('id' => $ledger->getId()));
        }

        return array(
            'ledger' => $ledger,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Ledger entity in a popup.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="ledger_new_popup")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newPopupAction(Request $request) {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a Ledger entity.
     *
     * @param Ledger $ledger
     *
     * @return array
     *
     * @Route("/{id}", name="ledger_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Ledger $ledger) {

        return array(
            'ledger' => $ledger,
        );
    }

    /**
     * Displays a form to edit an existing Ledger entity.
     *
     *
     * @param Request $request
     * @param Ledger $ledger
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="ledger_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction(Request $request, Ledger $ledger) {
        $editForm = $this->createForm(LedgerType::class, $ledger);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The ledger has been updated.');
            return $this->redirectToRoute('ledger_show', array('id' => $ledger->getId()));
        }

        return array(
            'ledger' => $ledger,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Ledger entity.
     *
     *
     * @param Request $request
     * @param Ledger $ledger
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="ledger_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, Ledger $ledger) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($ledger);
        $em->flush();
        $this->addFlash('success', 'The ledger was deleted.');

        return $this->redirectToRoute('ledger_index');
    }

}
