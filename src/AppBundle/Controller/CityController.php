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
use AppBundle\Entity\City;
use AppBundle\Form\CityType;

/**
 * City controller.
 *
 * @Route("/city")
 */
class CityController extends Controller {

    /**
     * Lists all City entities.
     *
     * @param Request $request
     *
     * @return array
     *
     * @Route("/", name="city_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(City::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $cities = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'cities' => $cities,
        );
    }

    /**
     * Typeahead API endpoint for City entities.
     *
     * To make this work, add something like this to CityRepository:
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
     * @Route("/typeahead", name="city_typeahead")
     * @Method("GET")
     * @return JsonResponse
     */
    public function typeahead(Request $request) {
        $q = $request->query->get('q');
        if (!$q) {
            return new JsonResponse([]);
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(City::class);
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
     * Search for City entities.
     *
     * To make this work, add a method like this one to the
     * AppBundle:City repository. Replace the fieldName with
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
     * @Route("/search", name="city_search")
     * @Method("GET")
     * @Template()
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:City');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $cities = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $cities = array();
        }

        return array(
            'cities' => $cities,
            'q' => $q,
        );
    }

    /**
     * Creates a new City entity.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="city_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request) {
        $city = new City();
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($city);
            $em->flush();

            $this->addFlash('success', 'The new city was created.');
            return $this->redirectToRoute('city_show', array('id' => $city->getId()));
        }

        return array(
            'city' => $city,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new City entity in a popup.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="city_new_popup")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newPopupAction(Request $request) {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a City entity.
     *
     * @param City $city
     *
     * @return array
     *
     * @Route("/{id}", name="city_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(City $city) {

        return array(
            'city' => $city,
        );
    }

    /**
     * Displays a form to edit an existing City entity.
     *
     *
     * @param Request $request
     * @param City $city
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="city_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction(Request $request, City $city) {
        $editForm = $this->createForm(CityType::class, $city);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The city has been updated.');
            return $this->redirectToRoute('city_show', array('id' => $city->getId()));
        }

        return array(
            'city' => $city,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a City entity.
     *
     *
     * @param Request $request
     * @param City $city
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="city_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, City $city) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($city);
        $em->flush();
        $this->addFlash('success', 'The city was deleted.');

        return $this->redirectToRoute('city_index');
    }

}
