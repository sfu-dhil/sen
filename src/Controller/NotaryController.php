<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Notary;
use App\Form\NotaryType;
use App\Repository\NotaryRepository;
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
 * Notary controller.
 *
 * @Route("/notary")
 */
class NotaryController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Lists all Notary entities.
     *
     * @return array
     *
     * @Route("/", name="notary_index", methods={"GET"})
     *
     * @Template
     */
    public function indexAction(Request $request, EntityManagerInterface $em) {
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Notary::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();

        $notaries = $this->paginator->paginate($query, $request->query->getint('page', 1), 25);

        return [
            'notaries' => $notaries,
        ];
    }

    /**
     * Typeahead API endpoint for Notary entities.
     *
     * To make this work, add something like this to NotaryRepository:
     *
     * @Route("/typeahead", name="notary_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, NotaryRepository $repo) {
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
     * Search for Notary entities.
     *
     * To make this work, add a method like this one to the
     * App:Notary repository. Replace the fieldName with
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
     * @Route("/search", name="notary_search", methods={"GET"})
     *
     * @Template
     */
    public function searchAction(Request $request, NotaryRepository $repo) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);

            $notaries = $this->paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $notaries = [];
        }

        return [
            'notaries' => $notaries,
            'q' => $q,
        ];
    }

    /**
     * Creates a new Notary entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="notary_new", methods={"GET", "POST"})
     *
     * @Template
     */
    public function newAction(Request $request, EntityManagerInterface $em) {
        $notary = new Notary();
        $form = $this->createForm(NotaryType::class, $notary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($notary);
            $em->flush();

            $this->addFlash('success', 'The new notary was created.');

            return $this->redirectToRoute('notary_show', ['id' => $notary->getId()]);
        }

        return [
            'notary' => $notary,
            'form' => $form->createView(),
        ];
    }

    /**
     * Creates a new Notary entity in a popup.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="notary_new_popup", methods={"GET", "POST"})
     *
     * @Template
     */
    public function newPopupAction(Request $request, EntityManagerInterface $em) {
        return $this->newAction($request, $em);
    }

    /**
     * Finds and displays a Notary entity.
     *
     * @return array
     *
     * @Route("/{id}", name="notary_show", methods={"GET"})
     *
     * @Template
     */
    public function showAction(Notary $notary) {
        return [
            'notary' => $notary,
        ];
    }

    /**
     * Displays a form to edit an existing Notary entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="notary_edit", methods={"GET", "POST"})
     *
     * @Template
     */
    public function editAction(Request $request, EntityManagerInterface $em, Notary $notary) {
        $editForm = $this->createForm(NotaryType::class, $notary);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The notary has been updated.');

            return $this->redirectToRoute('notary_show', ['id' => $notary->getId()]);
        }

        return [
            'notary' => $notary,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Deletes a Notary entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="notary_delete", methods={"GET"})
     */
    public function deleteAction(Request $request, EntityManagerInterface $em, Notary $notary) {
        $em->remove($notary);
        $em->flush();
        $this->addFlash('success', 'The notary was deleted.');

        return $this->redirectToRoute('notary_index');
    }
}
