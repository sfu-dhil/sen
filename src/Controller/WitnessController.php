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

use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/witness")
 */
class WitnessController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="witness_index", methods={"GET"})
     *
     * @Template
     */
    public function index(Request $request, WitnessRepository $witnessRepository) : array {
        $query = $witnessRepository->indexQuery();
        $pageSize = (int) $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'witnesses' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/new", name="witness_new", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $witness = new Witness();
        $form = $this->createForm(WitnessType::class, $witness);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($witness);
            $entityManager->flush();
            $this->addFlash('success', 'The new witness has been saved.');

            return $this->redirectToRoute('witness_show', ['id' => $witness->getId()]);
        }

        return [
            'witness' => $witness,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new_popup", name="witness_new_popup", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request) {
        return $this->new($request);
    }

    /**
     * @Route("/{id}", name="witness_show", methods={"GET"})
     * @Template
     *
     * @return array
     */
    public function show(Witness $witness) {
        return [
            'witness' => $witness,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="witness_edit", methods={"GET", "POST"})
     *
     * @Template
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, Witness $witness) {
        $form = $this->createForm(WitnessType::class, $witness);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated witness has been saved.');

            return $this->redirectToRoute('witness_show', ['id' => $witness->getId()]);
        }

        return [
            'witness' => $witness,
            'form' => $form->createView(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="witness_delete", methods={"DELETE"})
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Witness $witness) {
        if ($this->isCsrfTokenValid('delete' . $witness->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($witness);
            $entityManager->flush();
            $this->addFlash('success', 'The witness has been deleted.');
        }

        return $this->redirectToRoute('witness_index');
    }
}
