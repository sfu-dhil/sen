<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Witness;
use AppBundle\Form\WitnessType;

/**
 * Witness controller.
 *
 * @Route("/witness")
 */
class WitnessController extends Controller {

    /**
     * Lists all Witness entities.
     *
     * @param Request $request
     *
     * @return array
     *
     * @Route("/", name="witness_index", methods={"GET"})
     *
     * @Template()
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Witness::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $witnesses = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'witnesses' => $witnesses,
        );
    }

    /**
     * Typeahead API endpoint for Witness entities.
     *
     * To make this work, add something like this to WitnessRepository:
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
     * @Route("/typeahead", name="witness_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request) {
        $q = $request->query->get('q');
        if (!$q) {
            return new JsonResponse([]);
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Witness::class);
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
     * Search for Witness entities.
     *
     * To make this work, add a method like this one to the
     * AppBundle:Witness repository. Replace the fieldName with
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
     * @Route("/search", name="witness_search", methods={"GET"})
     *
     * @Template()
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Witness');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $witnesses = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $witnesses = array();
        }

        return array(
            'witnesses' => $witnesses,
            'q' => $q,
        );
    }

    /**
     * Creates a new Witness entity.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="witness_new", methods={"GET","POST"})
     *
     * @Template()
     */
    public function newAction(Request $request) {
        $witness = new Witness();
        $form = $this->createForm(WitnessType::class, $witness);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($witness);
            $em->flush();

            $this->addFlash('success', 'The new witness was created.');
            return $this->redirectToRoute('witness_show', array('id' => $witness->getId()));
        }

        return array(
            'witness' => $witness,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Witness entity in a popup.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="witness_new_popup", methods={"GET","POST"})
     *
     * @Template()
     */
    public function newPopupAction(Request $request) {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a Witness entity.
     *
     * @param Witness $witness
     *
     * @return array
     *
     * @Route("/{id}", name="witness_show", methods={"GET"})
     *
     * @Template()
     */
    public function showAction(Witness $witness) {

        return array(
            'witness' => $witness,
        );
    }

    /**
     * Displays a form to edit an existing Witness entity.
     *
     *
     * @param Request $request
     * @param Witness $witness
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="witness_edit", methods={"GET","POST"})
     *
     * @Template()
     */
    public function editAction(Request $request, Witness $witness) {
        $editForm = $this->createForm(WitnessType::class, $witness);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The witness has been updated.');
            return $this->redirectToRoute('witness_show', array('id' => $witness->getId()));
        }

        return array(
            'witness' => $witness,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Witness entity.
     *
     *
     * @param Request $request
     * @param Witness $witness
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="witness_delete", methods={"GET"})
     *
     */
    public function deleteAction(Request $request, Witness $witness) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($witness);
        $em->flush();
        $this->addFlash('success', 'The witness was deleted.');

        return $this->redirectToRoute('witness_index');
    }

}
