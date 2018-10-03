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
use AppBundle\Entity\WitnessCategory;
use AppBundle\Form\WitnessCategoryType;

/**
 * WitnessCategory controller.
 *
 * @Route("/witness_category")
 */
class WitnessCategoryController extends Controller {

    /**
     * Lists all WitnessCategory entities.
     *
     * @param Request $request
     *
     * @return array
     *
     * @Route("/", name="witness_category_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(WitnessCategory::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $witnessCategories = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'witnessCategories' => $witnessCategories,
        );
    }

    /**
     * Typeahead API endpoint for WitnessCategory entities.
     *
     * To make this work, add something like this to WitnessCategoryRepository:
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
     * @Route("/typeahead", name="witness_category_typeahead")
     * @Method("GET")
     * @return JsonResponse
     */
    public function typeahead(Request $request) {
        $q = $request->query->get('q');
        if (!$q) {
            return new JsonResponse([]);
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(WitnessCategory::class);
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
     * Search for WitnessCategory entities.
     *
     * To make this work, add a method like this one to the
     * AppBundle:WitnessCategory repository. Replace the fieldName with
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
     * @Route("/search", name="witness_category_search")
     * @Method("GET")
     * @Template()
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:WitnessCategory');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $witnessCategories = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $witnessCategories = array();
        }

        return array(
            'witnessCategories' => $witnessCategories,
            'q' => $q,
        );
    }

    /**
     * Creates a new WitnessCategory entity.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="witness_category_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request) {
        $witnessCategory = new WitnessCategory();
        $form = $this->createForm(WitnessCategoryType::class, $witnessCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($witnessCategory);
            $em->flush();

            $this->addFlash('success', 'The new witnessCategory was created.');
            return $this->redirectToRoute('witness_category_show', array('id' => $witnessCategory->getId()));
        }

        return array(
            'witnessCategory' => $witnessCategory,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new WitnessCategory entity in a popup.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="witness_category_new_popup")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newPopupAction(Request $request) {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a WitnessCategory entity.
     *
     * @param WitnessCategory $witnessCategory
     *
     * @return array
     *
     * @Route("/{id}", name="witness_category_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(WitnessCategory $witnessCategory) {

        return array(
            'witnessCategory' => $witnessCategory,
        );
    }

    /**
     * Displays a form to edit an existing WitnessCategory entity.
     *
     *
     * @param Request $request
     * @param WitnessCategory $witnessCategory
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="witness_category_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction(Request $request, WitnessCategory $witnessCategory) {
        $editForm = $this->createForm(WitnessCategoryType::class, $witnessCategory);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The witnessCategory has been updated.');
            return $this->redirectToRoute('witness_category_show', array('id' => $witnessCategory->getId()));
        }

        return array(
            'witnessCategory' => $witnessCategory,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a WitnessCategory entity.
     *
     *
     * @param Request $request
     * @param WitnessCategory $witnessCategory
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="witness_category_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, WitnessCategory $witnessCategory) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($witnessCategory);
        $em->flush();
        $this->addFlash('success', 'The witnessCategory was deleted.');

        return $this->redirectToRoute('witness_category_index');
    }

}
