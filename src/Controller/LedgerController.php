<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Ledger;
use App\Form\LedgerType;
use App\Repository\LedgerRepository;

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
 * @Route("/ledger")
 */
class LedgerController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="ledger_index", methods={"GET"})
     *
     * @Template
     */
    public function index(Request $request, LedgerRepository $ledgerRepository) : array {
        $query = $ledgerRepository->indexQuery();
        $pageSize = (int) $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'ledgers' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/typeahead", name="ledger_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, LedgerRepository $ledgerRepository) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];
        foreach ($ledgerRepository->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="ledger_new", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $ledger = new Ledger();
        $form = $this->createForm(LedgerType::class, $ledger);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ledger);
            $entityManager->flush();
            $this->addFlash('success', 'The new ledger has been saved.');

            return $this->redirectToRoute('ledger_show', ['id' => $ledger->getId()]);
        }

        return [
            'ledger' => $ledger,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new_popup", name="ledger_new_popup", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request) {
        return $this->new($request);
    }

    /**
     * @Route("/{id}", name="ledger_show", methods={"GET"})
     * @Template
     *
     * @return array
     */
    public function show(Ledger $ledger) {
        return [
            'ledger' => $ledger,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="ledger_edit", methods={"GET", "POST"})
     *
     * @Template
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, Ledger $ledger) {
        $form = $this->createForm(LedgerType::class, $ledger);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated ledger has been saved.');

            return $this->redirectToRoute('ledger_show', ['id' => $ledger->getId()]);
        }

        return [
            'ledger' => $ledger,
            'form' => $form->createView(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="ledger_delete", methods={"DELETE"})
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Ledger $ledger) {
        if ($this->isCsrfTokenValid('delete' . $ledger->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ledger);
            $entityManager->flush();
            $this->addFlash('success', 'The ledger has been deleted.');
        }

        return $this->redirectToRoute('ledger_index');
    }
}
