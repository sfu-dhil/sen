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
use AppBundle\Entity\LocationCategory;
use AppBundle\Form\LocationCategoryType;

/**
 * LocationCategory controller.
 *
 * @Security("has_role('ROLE_USER')")
 * @Route("/location_category")
 */
class LocationCategoryController extends Controller
{
    /**
     * Lists all LocationCategory entities.
     *
     * @param Request $request
     *
     * @return array
     *
     * @Route("/", name="location_category_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(LocationCategory::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $locationCategories = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'locationCategories' => $locationCategories,
        );
    }

/**
     * Typeahead API endpoint for LocationCategory entities.
     *
     * To make this work, add something like this to LocationCategoryRepository:
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
     * @Route("/typeahead", name="location_category_typeahead")
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
	$repo = $em->getRepository(LocationCategory::class);
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
     * Search for LocationCategory entities.
     *
     * To make this work, add a method like this one to the
     * AppBundle:LocationCategory repository. Replace the fieldName with
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
     * @Route("/search", name="location_category_search")
     * @Method("GET")
     * @Template()
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
	$repo = $em->getRepository('AppBundle:LocationCategory');
	$q = $request->query->get('q');
	if($q) {
	    $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $locationCategories = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
	} else {
            $locationCategories = array();
	}

        return array(
            'locationCategories' => $locationCategories,
            'q' => $q,
        );
    }

    /**
     * Creates a new LocationCategory entity.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="location_category_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request)
    {
        $locationCategory = new LocationCategory();
        $form = $this->createForm(LocationCategoryType::class, $locationCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($locationCategory);
            $em->flush();

            $this->addFlash('success', 'The new locationCategory was created.');
            return $this->redirectToRoute('location_category_show', array('id' => $locationCategory->getId()));
        }

        return array(
            'locationCategory' => $locationCategory,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new LocationCategory entity in a popup.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="location_category_new_popup")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newPopupAction(Request $request)
    {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a LocationCategory entity.
     *
     * @param LocationCategory $locationCategory
     *
     * @return array
     *
     * @Route("/{id}", name="location_category_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(LocationCategory $locationCategory)
    {

        return array(
            'locationCategory' => $locationCategory,
        );
    }

    /**
     * Displays a form to edit an existing LocationCategory entity.
     *
     *
     * @param Request $request
     * @param LocationCategory $locationCategory
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="location_category_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction(Request $request, LocationCategory $locationCategory)
    {
        $editForm = $this->createForm(LocationCategoryType::class, $locationCategory);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The locationCategory has been updated.');
            return $this->redirectToRoute('location_category_show', array('id' => $locationCategory->getId()));
        }

        return array(
            'locationCategory' => $locationCategory,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a LocationCategory entity.
     *
     *
     * @param Request $request
     * @param LocationCategory $locationCategory
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="location_category_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, LocationCategory $locationCategory)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($locationCategory);
        $em->flush();
        $this->addFlash('success', 'The locationCategory was deleted.');

        return $this->redirectToRoute('location_category_index');
    }
}
