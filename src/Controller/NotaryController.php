<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Notary;
use App\Form\NotaryType;
use App\Repository\NotaryRepository;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/notary")
 */
class NotaryController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="notary_index", methods={"GET"})
     *
     * @Template
     */
    public function index(Request $request, NotaryRepository $notaryRepository) : array {
        $query = $notaryRepository->indexQuery();
        $pageSize = (int) $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'notaries' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/typeahead", name="notary_typeahead", methods={"GET"})
     */
    public function typeahead(Request $request, NotaryRepository $notaryRepository) : JsonResponse {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];
        foreach ($notaryRepository->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="notary_new", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $notary = new Notary();
        $form = $this->createForm(NotaryType::class, $notary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($notary);
            $entityManager->flush();
            $this->addFlash('success', 'The new notary has been saved.');

            return $this->redirectToRoute('notary_show', ['id' => $notary->getId()]);
        }

        return [
            'notary' => $notary,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new_popup", name="notary_new_popup", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request) {
        return $this->new($request);
    }

    /**
     * @Route("/{id}", name="notary_show", methods={"GET"})
     * @Template
     */
    public function show(Notary $notary) : array {
        return [
            'notary' => $notary,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="notary_edit", methods={"GET", "POST"})
     *
     * @Template
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, Notary $notary) {
        $form = $this->createForm(NotaryType::class, $notary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated notary has been saved.');

            return $this->redirectToRoute('notary_show', ['id' => $notary->getId()]);
        }

        return [
            'notary' => $notary,
            'form' => $form->createView(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="notary_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Notary $notary) : RedirectResponse {
        if ($this->isCsrfTokenValid('delete' . $notary->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($notary);
            $entityManager->flush();
            $this->addFlash('success', 'The notary has been deleted.');
        }

        return $this->redirectToRoute('notary_index');
    }
}
