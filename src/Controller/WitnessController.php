<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Witness;
use App\Form\WitnessType;
use App\Repository\WitnessRepository;
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
 * Witness controller.
 *
 * @Route("/witness")
 */
class WitnessController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Lists all Witness entities.
     *
     * @return array
     *
     * @Route("/", name="witness_index", methods={"GET"})
     *
     * @Template
     */
    public function indexAction(Request $request, EntityManagerInterface $em) {
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Witness::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();

        $witnesses = $this->paginator->paginate($query, $request->query->getint('page', 1), 25);

        return [
            'witnesses' => $witnesses,
        ];
    }

    /**
     * Typeahead API endpoint for Witness entities.
     *
     * To make this work, add something like this to WitnessRepository:
     *
     * @Route("/typeahead", name="witness_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, WitnessRepository $repo) {
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
     * Search for Witness entities.
     *
     * To make this work, add a method like this one to the
     * App:Witness repository. Replace the fieldName with
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
     * @Route("/search", name="witness_search", methods={"GET"})
     *
     * @Template
     */
    public function searchAction(Request $request, WitnessRepository $repo) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);

            $witnesses = $this->paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $witnesses = [];
        }

        return [
            'witnesses' => $witnesses,
            'q' => $q,
        ];
    }

    /**
     * Creates a new Witness entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="witness_new", methods={"GET", "POST"})
     *
     * @Template
     */
    public function newAction(Request $request, EntityManagerInterface $em) {
        $witness = new Witness();
        $form = $this->createForm(WitnessType::class, $witness);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($witness);
            $em->flush();

            $this->addFlash('success', 'The new witness was created.');

            return $this->redirectToRoute('witness_show', ['id' => $witness->getId()]);
        }

        return [
            'witness' => $witness,
            'form' => $form->createView(),
        ];
    }

    /**
     * Creates a new Witness entity in a popup.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="witness_new_popup", methods={"GET", "POST"})
     *
     * @Template
     */
    public function newPopupAction(Request $request, EntityManagerInterface $em) {
        return $this->newAction($request, $em);
    }

    /**
     * Finds and displays a Witness entity.
     *
     * @return array
     *
     * @Route("/{id}", name="witness_show", methods={"GET"})
     *
     * @Template
     */
    public function showAction(Witness $witness) {
        return [
            'witness' => $witness,
        ];
    }

    /**
     * Displays a form to edit an existing Witness entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="witness_edit", methods={"GET", "POST"})
     *
     * @Template
     */
    public function editAction(Request $request, EntityManagerInterface $em, Witness $witness) {
        $editForm = $this->createForm(WitnessType::class, $witness);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The witness has been updated.');

            return $this->redirectToRoute('witness_show', ['id' => $witness->getId()]);
        }

        return [
            'witness' => $witness,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Deletes a Witness entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="witness_delete", methods={"GET"})
     */
    public function deleteAction(Request $request, EntityManagerInterface $em, Witness $witness) {
        $em->remove($witness);
        $em->flush();
        $this->addFlash('success', 'The witness was deleted.');

        return $this->redirectToRoute('witness_index');
    }
}
