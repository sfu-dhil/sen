<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\WitnessCategory;
use App\Form\WitnessCategoryType;
use App\Repository\WitnessCategoryRepository;
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
 * WitnessCategory controller.
 *
 * @Route("/witness_category")
 */
class WitnessCategoryController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Lists all WitnessCategory entities.
     *
     * @return array
     *
     * @Route("/", name="witness_category_index", methods={"GET"})
     *
     * @Template
     */
    public function indexAction(Request $request, EntityManagerInterface $em) {
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(WitnessCategory::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();

        $witnessCategories = $this->paginator->paginate($query, $request->query->getint('page', 1), 25);

        return [
            'witnessCategories' => $witnessCategories,
        ];
    }

    /**
     * Typeahead API endpoint for WitnessCategory entities.
     *
     * To make this work, add something like this to WitnessCategoryRepository:
     *
     * @Route("/typeahead", name="witness_category_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, WitnessCategoryRepository $repo) {
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
     * Search for WitnessCategory entities.
     *
     * To make this work, add a method like this one to the
     * App:WitnessCategory repository. Replace the fieldName with
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
     * @Route("/search", name="witness_category_search", methods={"GET"})
     *
     * @Template
     */
    public function searchAction(Request $request, WitnessCategoryRepository $repo) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);

            $witnessCategories = $this->paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $witnessCategories = [];
        }

        return [
            'witnessCategories' => $witnessCategories,
            'q' => $q,
        ];
    }

    /**
     * Creates a new WitnessCategory entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="witness_category_new", methods={"GET", "POST"})
     *
     * @Template
     */
    public function newAction(Request $request, EntityManagerInterface $em) {
        $witnessCategory = new WitnessCategory();
        $form = $this->createForm(WitnessCategoryType::class, $witnessCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($witnessCategory);
            $em->flush();

            $this->addFlash('success', 'The new witnessCategory was created.');

            return $this->redirectToRoute('witness_category_show', ['id' => $witnessCategory->getId()]);
        }

        return [
            'witnessCategory' => $witnessCategory,
            'form' => $form->createView(),
        ];
    }

    /**
     * Creates a new WitnessCategory entity in a popup.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="witness_category_new_popup", methods={"GET", "POST"})
     *
     * @Template
     */
    public function newPopupAction(Request $request, EntityManagerInterface $em) {
        return $this->newAction($request, $em);
    }

    /**
     * Finds and displays a WitnessCategory entity.
     *
     * @return array
     *
     * @Route("/{id}", name="witness_category_show", methods={"GET"})
     *
     * @Template
     */
    public function showAction(WitnessCategory $witnessCategory) {
        return [
            'witnessCategory' => $witnessCategory,
        ];
    }

    /**
     * Displays a form to edit an existing WitnessCategory entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="witness_category_edit", methods={"GET", "POST"})
     *
     * @Template
     */
    public function editAction(Request $request, EntityManagerInterface $em, WitnessCategory $witnessCategory) {
        $editForm = $this->createForm(WitnessCategoryType::class, $witnessCategory);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The witnessCategory has been updated.');

            return $this->redirectToRoute('witness_category_show', ['id' => $witnessCategory->getId()]);
        }

        return [
            'witnessCategory' => $witnessCategory,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Deletes a WitnessCategory entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="witness_category_delete", methods={"GET"})
     */
    public function deleteAction(Request $request, EntityManagerInterface $em, WitnessCategory $witnessCategory) {
        $em->remove($witnessCategory);
        $em->flush();
        $this->addFlash('success', 'The witnessCategory was deleted.');

        return $this->redirectToRoute('witness_category_index');
    }
}
