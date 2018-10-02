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
use AppBundle\Entity\Residence;
use AppBundle\Form\ResidenceType;

/**
 * Residence controller.
 *
 * @Security("has_role('ROLE_USER')")
 * @Route("/residence")
 */
class ResidenceController extends Controller
{
    /**
     * Lists all Residence entities.
     *
     * @param Request $request
     *
     * @return array
     *
     * @Route("/", name="residence_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Residence::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $residences = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'residences' => $residences,
        );
    }

/**
     * Typeahead API endpoint for Residence entities.
     *
     * To make this work, add something like this to ResidenceRepository:
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
     * @Route("/typeahead", name="residence_typeahead")
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
	$repo = $em->getRepository(Residence::class);
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
     * Search for Residence entities.
     *
     * To make this work, add a method like this one to the
     * AppBundle:Residence repository. Replace the fieldName with
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
     * @Route("/search", name="residence_search")
     * @Method("GET")
     * @Template()
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
	$repo = $em->getRepository('AppBundle:Residence');
	$q = $request->query->get('q');
	if($q) {
	    $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $residences = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
	} else {
            $residences = array();
	}

        return array(
            'residences' => $residences,
            'q' => $q,
        );
    }

    /**
     * Creates a new Residence entity.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="residence_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request)
    {
        $residence = new Residence();
        $form = $this->createForm(ResidenceType::class, $residence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($residence);
            $em->flush();

            $this->addFlash('success', 'The new residence was created.');
            return $this->redirectToRoute('residence_show', array('id' => $residence->getId()));
        }

        return array(
            'residence' => $residence,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Residence entity in a popup.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="residence_new_popup")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newPopupAction(Request $request)
    {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a Residence entity.
     *
     * @param Residence $residence
     *
     * @return array
     *
     * @Route("/{id}", name="residence_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Residence $residence)
    {

        return array(
            'residence' => $residence,
        );
    }

    /**
     * Displays a form to edit an existing Residence entity.
     *
     *
     * @param Request $request
     * @param Residence $residence
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="residence_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction(Request $request, Residence $residence)
    {
        $editForm = $this->createForm(ResidenceType::class, $residence);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The residence has been updated.');
            return $this->redirectToRoute('residence_show', array('id' => $residence->getId()));
        }

        return array(
            'residence' => $residence,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Residence entity.
     *
     *
     * @param Request $request
     * @param Residence $residence
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="residence_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, Residence $residence)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($residence);
        $em->flush();
        $this->addFlash('success', 'The residence was deleted.');

        return $this->redirectToRoute('residence_index');
    }
}
