<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;


use App\Entity\RelationshipCategory;
use App\Form\RelationshipCategoryType;
use App\Repository\RelationshipCategoryRepository;
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
 * RelationshipCategory controller.
 *
 * @Route("/relationship_category")
 */
class RelationshipCategoryController extends AbstractController  implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Lists all RelationshipCategory entities.
     *
     * @return array
     *
     * @Route("/", name="relationship_category_index", methods={"GET"})
     *
     * @Template()
     */
    public function indexAction(Request $request, EntityManagerInterface $em) {

        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(RelationshipCategory::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();

        $relationshipCategories = $this->paginator->paginate($query, $request->query->getint('page', 1), 25);

        return [
            'relationshipCategories' => $relationshipCategories,
        ];
    }

    /**
     * Typeahead API endpoint for RelationshipCategory entities.
     *
     * To make this work, add something like this to RelationshipCategoryRepository:
     *
     * @Route("/typeahead", name="relationship_category_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, RelationshipCategoryRepository $repo) {
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
     * Search for RelationshipCategory entities.
     *
     * To make this work, add a method like this one to the
     * App:RelationshipCategory repository. Replace the fieldName with
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
     * @Route("/search", name="relationship_category_search", methods={"GET"})
     *
     * @Template()
     */
    public function searchAction(Request $request, RelationshipCategoryRepository $repo) {

        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);

            $relationshipCategories = $this->paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $relationshipCategories = [];
        }

        return [
            'relationshipCategories' => $relationshipCategories,
            'q' => $q,
        ];
    }

    /**
     * Creates a new RelationshipCategory entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="relationship_category_new", methods={"GET","POST"})
     *
     * @Template()
     */
    public function newAction(Request $request, EntityManagerInterface $em) {
        $relationshipCategory = new RelationshipCategory();
        $form = $this->createForm(RelationshipCategoryType::class, $relationshipCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($relationshipCategory);
            $em->flush();

            $this->addFlash('success', 'The new relationshipCategory was created.');

            return $this->redirectToRoute('relationship_category_show', ['id' => $relationshipCategory->getId()]);
        }

        return [
            'relationshipCategory' => $relationshipCategory,
            'form' => $form->createView(),
        ];
    }

    /**
     * Creates a new RelationshipCategory entity in a popup.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="relationship_category_new_popup", methods={"GET","POST"})
     *
     * @Template()
     */
    public function newPopupAction(Request $request) {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a RelationshipCategory entity.
     *
     * @return array
     *
     * @Route("/{id}", name="relationship_category_show", methods={"GET"})
     *
     * @Template()
     */
    public function showAction(RelationshipCategory $relationshipCategory) {
        return [
            'relationshipCategory' => $relationshipCategory,
        ];
    }

    /**
     * Displays a form to edit an existing RelationshipCategory entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="relationship_category_edit", methods={"GET","POST"})
     *
     * @Template()
     */
    public function editAction(Request $request, EntityManagerInterface $em, RelationshipCategory $relationshipCategory) {
        $editForm = $this->createForm(RelationshipCategoryType::class, $relationshipCategory);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $em->flush();
            $this->addFlash('success', 'The relationshipCategory has been updated.');

            return $this->redirectToRoute('relationship_category_show', ['id' => $relationshipCategory->getId()]);
        }

        return [
            'relationshipCategory' => $relationshipCategory,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Deletes a RelationshipCategory entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="relationship_category_delete", methods={"GET"})
     */
    public function deleteAction(Request $request, EntityManagerInterface $em, RelationshipCategory $relationshipCategory) {

        $em->remove($relationshipCategory);
        $em->flush();
        $this->addFlash('success', 'The relationshipCategory was deleted.');

        return $this->redirectToRoute('relationship_category_index');
    }
}
