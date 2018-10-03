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
use AppBundle\Entity\Notary;
use AppBundle\Form\NotaryType;

/**
 * Notary controller.
 *
 * @Route("/notary")
 */
class NotaryController extends Controller {

    /**
     * Lists all Notary entities.
     *
     * @param Request $request
     *
     * @return array
     *
     * @Route("/", name="notary_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Notary::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $notaries = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'notaries' => $notaries,
        );
    }

    /**
     * Typeahead API endpoint for Notary entities.
     *
     * To make this work, add something like this to NotaryRepository:
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
     * @Route("/typeahead", name="notary_typeahead")
     * @Method("GET")
     * @return JsonResponse
     */
    public function typeahead(Request $request) {
        $q = $request->query->get('q');
        if (!$q) {
            return new JsonResponse([]);
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Notary::class);
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
     * Search for Notary entities.
     *
     * To make this work, add a method like this one to the
     * AppBundle:Notary repository. Replace the fieldName with
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
     * @Route("/search", name="notary_search")
     * @Method("GET")
     * @Template()
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Notary');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $notaries = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $notaries = array();
        }

        return array(
            'notaries' => $notaries,
            'q' => $q,
        );
    }

    /**
     * Creates a new Notary entity.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="notary_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request) {
        $notary = new Notary();
        $form = $this->createForm(NotaryType::class, $notary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($notary);
            $em->flush();

            $this->addFlash('success', 'The new notary was created.');
            return $this->redirectToRoute('notary_show', array('id' => $notary->getId()));
        }

        return array(
            'notary' => $notary,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Notary entity in a popup.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="notary_new_popup")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newPopupAction(Request $request) {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a Notary entity.
     *
     * @param Notary $notary
     *
     * @return array
     *
     * @Route("/{id}", name="notary_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Notary $notary) {

        return array(
            'notary' => $notary,
        );
    }

    /**
     * Displays a form to edit an existing Notary entity.
     *
     *
     * @param Request $request
     * @param Notary $notary
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="notary_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction(Request $request, Notary $notary) {
        $editForm = $this->createForm(NotaryType::class, $notary);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The notary has been updated.');
            return $this->redirectToRoute('notary_show', array('id' => $notary->getId()));
        }

        return array(
            'notary' => $notary,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Notary entity.
     *
     *
     * @param Request $request
     * @param Notary $notary
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="notary_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, Notary $notary) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($notary);
        $em->flush();
        $this->addFlash('success', 'The notary was deleted.');

        return $this->redirectToRoute('notary_index');
    }

}
