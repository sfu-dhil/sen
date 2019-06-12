<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Relationship;
use AppBundle\Form\RelationshipType;

/**
 * Relationship controller.
 *
 * @Route("/relationship")
 */
class RelationshipController extends Controller
{
    /**
     * Lists all Relationship entities.
     *
     * @param Request $request
     *
     * @return array
     *
     * @Route("/", name="relationship_index", methods={"GET"})
     *
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Relationship::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $relationships = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'relationships' => $relationships,
        );
    }

    /**
     * Search for Relationship entities.
     *
     * To make this work, add a method like this one to the
     * AppBundle:Relationship repository. Replace the fieldName with
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
     * @Route("/search", name="relationship_search", methods={"GET"})
     *
     * @Template()
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
	$repo = $em->getRepository('AppBundle:Relationship');
	$q = $request->query->get('q');
	if($q) {
	    $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $relationships = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
	} else {
            $relationships = array();
	}

        return array(
            'relationships' => $relationships,
            'q' => $q,
        );
    }

    /**
     * Creates a new Relationship entity.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="relationship_new", methods={"GET","POST"})
     *
     * @Template()
     */
    public function newAction(Request $request)
    {
        $relationship = new Relationship();
        $form = $this->createForm(RelationshipType::class, $relationship);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($relationship);
            $em->flush();

            $this->addFlash('success', 'The new relationship was created.');
            return $this->redirectToRoute('relationship_show', array('id' => $relationship->getId()));
        }

        return array(
            'relationship' => $relationship,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Relationship entity in a popup.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="relationship_new_popup", methods={"GET","POST"})
     *
     * @Template()
     */
    public function newPopupAction(Request $request)
    {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a Relationship entity.
     *
     * @param Relationship $relationship
     *
     * @return array
     *
     * @Route("/{id}", name="relationship_show", methods={"GET"})
     *
     * @Template()
     */
    public function showAction(Relationship $relationship)
    {

        return array(
            'relationship' => $relationship,
        );
    }

    /**
     * Displays a form to edit an existing Relationship entity.
     *
     *
     * @param Request $request
     * @param Relationship $relationship
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="relationship_edit", methods={"GET","POST"})
     *
     * @Template()
     */
    public function editAction(Request $request, Relationship $relationship)
    {
        $editForm = $this->createForm(RelationshipType::class, $relationship);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The relationship has been updated.');
            return $this->redirectToRoute('relationship_show', array('id' => $relationship->getId()));
        }

        return array(
            'relationship' => $relationship,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Relationship entity.
     *
     *
     * @param Request $request
     * @param Relationship $relationship
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="relationship_delete", methods={"GET"})
     *
     */
    public function deleteAction(Request $request, Relationship $relationship)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($relationship);
        $em->flush();
        $this->addFlash('success', 'The relationship was deleted.');

        return $this->redirectToRoute('relationship_index');
    }
}
