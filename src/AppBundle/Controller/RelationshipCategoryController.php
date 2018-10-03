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
use AppBundle\Entity\RelationshipCategory;
use AppBundle\Form\RelationshipCategoryType;

/**
 * RelationshipCategory controller.
 *
 * @Route("/relationship_category")
 */
class RelationshipCategoryController extends Controller {

    /**
     * Lists all RelationshipCategory entities.
     *
     * @param Request $request
     *
     * @return array
     *
     * @Route("/", name="relationship_category_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(RelationshipCategory::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $relationshipCategories = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'relationshipCategories' => $relationshipCategories,
        );
    }

    /**
     * Typeahead API endpoint for RelationshipCategory entities.
     *
     * To make this work, add something like this to RelationshipCategoryRepository:
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
     * @Route("/typeahead", name="relationship_category_typeahead")
     * @Method("GET")
     * @return JsonResponse
     */
    public function typeahead(Request $request) {
        $q = $request->query->get('q');
        if (!$q) {
            return new JsonResponse([]);
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(RelationshipCategory::class);
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
     * Search for RelationshipCategory entities.
     *
     * To make this work, add a method like this one to the
     * AppBundle:RelationshipCategory repository. Replace the fieldName with
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
     * @Route("/search", name="relationship_category_search")
     * @Method("GET")
     * @Template()
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:RelationshipCategory');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $relationshipCategories = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $relationshipCategories = array();
        }

        return array(
            'relationshipCategories' => $relationshipCategories,
            'q' => $q,
        );
    }

    /**
     * Creates a new RelationshipCategory entity.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="relationship_category_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request) {
        $relationshipCategory = new RelationshipCategory();
        $form = $this->createForm(RelationshipCategoryType::class, $relationshipCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($relationshipCategory);
            $em->flush();

            $this->addFlash('success', 'The new relationshipCategory was created.');
            return $this->redirectToRoute('relationship_category_show', array('id' => $relationshipCategory->getId()));
        }

        return array(
            'relationshipCategory' => $relationshipCategory,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new RelationshipCategory entity in a popup.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="relationship_category_new_popup")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newPopupAction(Request $request) {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a RelationshipCategory entity.
     *
     * @param RelationshipCategory $relationshipCategory
     *
     * @return array
     *
     * @Route("/{id}", name="relationship_category_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(RelationshipCategory $relationshipCategory) {

        return array(
            'relationshipCategory' => $relationshipCategory,
        );
    }

    /**
     * Displays a form to edit an existing RelationshipCategory entity.
     *
     *
     * @param Request $request
     * @param RelationshipCategory $relationshipCategory
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="relationship_category_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction(Request $request, RelationshipCategory $relationshipCategory) {
        $editForm = $this->createForm(RelationshipCategoryType::class, $relationshipCategory);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The relationshipCategory has been updated.');
            return $this->redirectToRoute('relationship_category_show', array('id' => $relationshipCategory->getId()));
        }

        return array(
            'relationshipCategory' => $relationshipCategory,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a RelationshipCategory entity.
     *
     *
     * @param Request $request
     * @param RelationshipCategory $relationshipCategory
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="relationship_category_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, RelationshipCategory $relationshipCategory) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($relationshipCategory);
        $em->flush();
        $this->addFlash('success', 'The relationshipCategory was deleted.');

        return $this->redirectToRoute('relationship_category_index');
    }

}
