<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Relationship;
use App\Form\RelationshipType;
use App\Repository\RelationshipRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Relationship controller.
 *
 * @Route("/relationship")
 */
class RelationshipController extends AbstractController implements PaginatorAwareInterface
{
    use PaginatorTrait;

    /**
     * Lists all Relationship entities.
     *
     * @return array
     *
     * @Route("/", name="relationship_index", methods={"GET"})
     *
     * @Template
     */
    public function indexAction(Request $request, EntityManagerInterface $em) {
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Relationship::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();

        $relationships = $this->paginator->paginate($query, $request->query->getint('page', 1), 25);

        return [
            'relationships' => $relationships,
        ];
    }

    /**
     * Search for Relationship entities.
     *
     * To make this work, add a method like this one to the
     * App:Relationship repository. Replace the fieldName with
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
     * @Route("/search", name="relationship_search", methods={"GET"})
     *
     * @Template
     */
    public function searchAction(Request $request, RelationshipRepository $repo) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);

            $relationships = $this->paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $relationships = [];
        }

        return [
            'relationships' => $relationships,
            'q' => $q,
        ];
    }

    /**
     * Creates a new Relationship entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="relationship_new", methods={"GET", "POST"})
     *
     * @Template
     */
    public function newAction(Request $request, EntityManagerInterface $em) {
        $relationship = new Relationship();
        $form = $this->createForm(RelationshipType::class, $relationship);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($relationship);
            $em->flush();

            $this->addFlash('success', 'The new relationship was created.');

            return $this->redirectToRoute('relationship_show', ['id' => $relationship->getId()]);
        }

        return [
            'relationship' => $relationship,
            'form' => $form->createView(),
        ];
    }

    /**
     * Creates a new Relationship entity in a popup.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="relationship_new_popup", methods={"GET", "POST"})
     *
     * @Template
     */
    public function newPopupAction(Request $request, EntityManagerInterface $em) {
        return $this->newAction($request, $em);
    }

    /**
     * Finds and displays a Relationship entity.
     *
     * @return array
     *
     * @Route("/{id}", name="relationship_show", methods={"GET"})
     *
     * @Template
     */
    public function showAction(Relationship $relationship) {
        return [
            'relationship' => $relationship,
        ];
    }

    /**
     * Displays a form to edit an existing Relationship entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="relationship_edit", methods={"GET", "POST"})
     *
     * @Template
     */
    public function editAction(Request $request, EntityManagerInterface $em, Relationship $relationship) {
        $editForm = $this->createForm(RelationshipType::class, $relationship);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The relationship has been updated.');

            return $this->redirectToRoute('relationship_show', ['id' => $relationship->getId()]);
        }

        return [
            'relationship' => $relationship,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Deletes a Relationship entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="relationship_delete", methods={"GET"})
     */
    public function deleteAction(Request $request, EntityManagerInterface $em, Relationship $relationship) {
        $em->remove($relationship);
        $em->flush();
        $this->addFlash('success', 'The relationship was deleted.');

        return $this->redirectToRoute('relationship_index');
    }
}
