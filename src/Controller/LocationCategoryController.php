<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\LocationCategory;
use App\Form\LocationCategoryType;
use App\Repository\LocationCategoryRepository;
use App\Repository\LocationRepository;
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
 * LocationCategory controller.
 *
 * @Route("/location_category")
 */
class LocationCategoryController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Lists all LocationCategory entities.
     *
     * @return array
     *
     * @Route("/", name="location_category_index", methods={"GET"})
     *
     * @Template()
     */
    public function indexAction(Request $request, EntityManagerInterface $em) {
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(LocationCategory::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();

        $locationCategories = $this->paginator->paginate($query, $request->query->getint('page', 1), 25);

        return [
            'locationCategories' => $locationCategories,
        ];
    }

    /**
     * Typeahead API endpoint for LocationCategory entities.
     *
     * To make this work, add something like this to LocationCategoryRepository:
     *
     * @Route("/typeahead", name="location_category_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, LocationRepository $repo) {
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
     * Search for LocationCategory entities.
     *
     * To make this work, add a method like this one to the
     * App:LocationCategory repository. Replace the fieldName with
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
     * @Route("/search", name="location_category_search", methods={"GET"})
     *
     * @Template()
     */
    public function searchAction(Request $request, LocationCategoryRepository $repo) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);

            $locationCategories = $this->paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $locationCategories = [];
        }

        return [
            'locationCategories' => $locationCategories,
            'q' => $q,
        ];
    }

    /**
     * Creates a new LocationCategory entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="location_category_new", methods={"GET","POST"})
     *
     * @Template()
     */
    public function newAction(Request $request, EntityManagerInterface $em) {
        $locationCategory = new LocationCategory();
        $form = $this->createForm(LocationCategoryType::class, $locationCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($locationCategory);
            $em->flush();

            $this->addFlash('success', 'The new locationCategory was created.');

            return $this->redirectToRoute('location_category_show', ['id' => $locationCategory->getId()]);
        }

        return [
            'locationCategory' => $locationCategory,
            'form' => $form->createView(),
        ];
    }

    /**
     * Creates a new LocationCategory entity in a popup.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="location_category_new_popup", methods={"GET","POST"})
     *
     * @Template()
     */
    public function newPopupAction(Request $request) {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a LocationCategory entity.
     *
     * @return array
     *
     * @Route("/{id}", name="location_category_show", methods={"GET"})
     *
     * @Template()
     */
    public function showAction(LocationCategory $locationCategory) {
        return [
            'locationCategory' => $locationCategory,
        ];
    }

    /**
     * Displays a form to edit an existing LocationCategory entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="location_category_edit", methods={"GET","POST"})
     *
     * @Template()
     */
    public function editAction(Request $request, EntityManagerInterface $em, LocationCategory $locationCategory) {
        $editForm = $this->createForm(LocationCategoryType::class, $locationCategory);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The locationCategory has been updated.');

            return $this->redirectToRoute('location_category_show', ['id' => $locationCategory->getId()]);
        }

        return [
            'locationCategory' => $locationCategory,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Deletes a LocationCategory entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="location_category_delete", methods={"GET"})
     */
    public function deleteAction(Request $request, EntityManagerInterface $em, LocationCategory $locationCategory) {
        $em->remove($locationCategory);
        $em->flush();
        $this->addFlash('success', 'The locationCategory was deleted.');

        return $this->redirectToRoute('location_category_index');
    }
}
