<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Relationship;
use App\Form\RelationshipType;
use App\Repository\RelationshipRepository;

use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/relationship")
 */
class RelationshipController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="relationship_index", methods={"GET"})
     *
     * @Template
     */
    public function index(Request $request, RelationshipRepository $relationshipRepository) : array {
        $query = $relationshipRepository->indexQuery();
        $pageSize = (int) $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'relationships' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/new", name="relationship_new", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $relationship = new Relationship();
        $form = $this->createForm(RelationshipType::class, $relationship);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($relationship);
            $entityManager->flush();
            $this->addFlash('success', 'The new relationship has been saved.');

            return $this->redirectToRoute('relationship_show', ['id' => $relationship->getId()]);
        }

        return [
            'relationship' => $relationship,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new_popup", name="relationship_new_popup", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request) {
        return $this->new($request);
    }

    /**
     * @Route("/{id}", name="relationship_show", methods={"GET"})
     * @Template
     *
     * @return array
     */
    public function show(Relationship $relationship) {
        return [
            'relationship' => $relationship,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="relationship_edit", methods={"GET", "POST"})
     *
     * @Template
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, Relationship $relationship) {
        $form = $this->createForm(RelationshipType::class, $relationship);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated relationship has been saved.');

            return $this->redirectToRoute('relationship_show', ['id' => $relationship->getId()]);
        }

        return [
            'relationship' => $relationship,
            'form' => $form->createView(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="relationship_delete", methods={"DELETE"})
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Relationship $relationship) {
        if ($this->isCsrfTokenValid('delete' . $relationship->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($relationship);
            $entityManager->flush();
            $this->addFlash('success', 'The relationship has been deleted.');
        }

        return $this->redirectToRoute('relationship_index');
    }
}
