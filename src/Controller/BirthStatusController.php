<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\BirthStatus;
use App\Form\BirthStatusType;
use App\Repository\BirthStatusRepository;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/birth_status")
 */
class BirthStatusController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="birth_status_index", methods={"GET"})
     *
     * @Template
     */
    public function index(Request $request, BirthStatusRepository $birthStatusRepository) : array {
        $query = $birthStatusRepository->indexQuery();
        $pageSize = (int) $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'birth_statuses' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/search", name="birth_status_search", methods={"GET"})
     *
     * @Template
     */
    public function search(Request $request, BirthStatusRepository $birthStatusRepository) : array {
        $q = $request->query->get('q');
        if ($q) {
            $query = $birthStatusRepository->searchQuery($q);
            $birthStatuses = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);
        } else {
            $birthStatuses = [];
        }

        return [
            'birth_statuses' => $birthStatuses,
            'q' => $q,
        ];
    }

    /**
     * @Route("/typeahead", name="birth_status_typeahead", methods={"GET"})
     */
    public function typeahead(Request $request, BirthStatusRepository $birthStatusRepository) : JsonResponse {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];
        foreach ($birthStatusRepository->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="birth_status_new", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $birthStatus = new BirthStatus();
        $form = $this->createForm(BirthStatusType::class, $birthStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($birthStatus);
            $entityManager->flush();
            $this->addFlash('success', 'The new birthStatus has been saved.');

            return $this->redirectToRoute('birth_status_show', ['id' => $birthStatus->getId()]);
        }

        return [
            'birth_status' => $birthStatus,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new_popup", name="birth_status_new_popup", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request) {
        return $this->new($request);
    }

    /**
     * @Route("/{id}", name="birth_status_show", methods={"GET"})
     * @Template
     */
    public function show(BirthStatus $birthStatus) : array {
        return [
            'birth_status' => $birthStatus,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="birth_status_edit", methods={"GET", "POST"})
     *
     * @Template
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, BirthStatus $birthStatus) {
        $form = $this->createForm(BirthStatusType::class, $birthStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated birthStatus has been saved.');

            return $this->redirectToRoute('birth_status_show', ['id' => $birthStatus->getId()]);
        }

        return [
            'birth_status' => $birthStatus,
            'form' => $form->createView(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="birth_status_delete", methods={"DELETE"})
     */
    public function delete(Request $request, BirthStatus $birthStatus) : RedirectResponse {
        if ($this->isCsrfTokenValid('delete' . $birthStatus->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($birthStatus);
            $entityManager->flush();
            $this->addFlash('success', 'The birthStatus has been deleted.');
        }

        return $this->redirectToRoute('birth_status_index');
    }
}
