<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\City;
use App\Form\CityType;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * City controller.
 *
 * @Route("/city")
 */
class CityController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Lists all City entities.
     *
     * @return array
     *
     * @Route("/", name="city_index", methods={"GET"})
     *
     * @Template()
     */
    public function indexAction(Request $request, EntityManagerInterface $em) {
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(City::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();

        $cities = $this->paginator->paginate($query, $request->query->getint('page', 1), 25);

        return [
            'cities' => $cities,
        ];
    }

    /**
     * Typeahead API endpoint for City entities.
     *
     * To make this work, add something like this to CityRepository:
     *
     * @Route("/typeahead", name="city_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, CityRepository $repository) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }

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
     * App:City repository. Replace the fieldName with
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
     * @Route("/search", name="city_search", methods={"GET"})
     *
     * @Template()
     */
    public function searchAction(Request $request, CityRepository $repo) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);

            $cities = $this->paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $cities = [];
        }

        return [
            'cities' => $cities,
            'q' => $q,
        ];
    }

    /**
     * Creates a new City entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="city_new", methods={"GET","POST"})
     *
     * @Template()
     */
    public function newAction(Request $request, EntityManagerInterface $em) {
        $city = new City();
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($city);
            $em->flush();

            $this->addFlash('success', 'The new city was created.');

            return $this->redirectToRoute('city_show', ['id' => $city->getId()]);
        }

        return [
            'city' => $city,
            'form' => $form->createView(),
        ];
    }

    /**
     * Creates a new City entity in a popup.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="city_new_popup", methods={"GET","POST"})
     *
     * @Template()
     */
    public function newPopupAction(Request $request) {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a City entity.
     *
     * @return array
     *
     * @Route("/{id}", name="city_show", methods={"GET"})
     *
     * @Template()
     */
    public function showAction(City $city) {
        return [
            'city' => $city,
        ];
    }

    /**
     * Displays a form to edit an existing City entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="city_edit", methods={"GET","POST"})
     *
     * @Template()
     */
    public function editAction(Request $request, EntityManagerInterface $em, City $city) {
        $editForm = $this->createForm(CityType::class, $city);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The city has been updated.');

            return $this->redirectToRoute('city_show', ['id' => $city->getId()]);
        }

        return [
            'city' => $city,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Deletes a City entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="city_delete", methods={"GET"})
     */
    public function deleteAction(Request $request, EntityManagerInterface $em, City $city) {
        $em->remove($city);
        $em->flush();
        $this->addFlash('success', 'The city was deleted.');

        return $this->redirectToRoute('city_index');
    }
}
