<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Residence;
use App\Form\ResidenceType;
use App\Repository\ResidenceRepository;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/residence")
 */
class ResidenceController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="residence_index", methods={"GET"})
     *
     * @Template
     */
    public function index(Request $request, ResidenceRepository $residenceRepository) : array {
        $query = $residenceRepository->indexQuery();
        $pageSize = (int) $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'residences' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/new", name="residence_new", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $residence = new Residence();
        $form = $this->createForm(ResidenceType::class, $residence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($residence);
            $entityManager->flush();
            $this->addFlash('success', 'The new residence has been saved.');

            return $this->redirectToRoute('residence_show', ['id' => $residence->getId()]);
        }

        return [
            'residence' => $residence,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new_popup", name="residence_new_popup", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request) {
        return $this->new($request);
    }

    /**
     * @Route("/{id}", name="residence_show", methods={"GET"})
     * @Template
     */
    public function show(Residence $residence) : array {
        return [
            'residence' => $residence,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="residence_edit", methods={"GET", "POST"})
     *
     * @Template
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, Residence $residence) {
        $form = $this->createForm(ResidenceType::class, $residence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated residence has been saved.');

            return $this->redirectToRoute('residence_show', ['id' => $residence->getId()]);
        }

        return [
            'residence' => $residence,
            'form' => $form->createView(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="residence_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Residence $residence) : RedirectResponse {
        if ($this->isCsrfTokenValid('delete' . $residence->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($residence);
            $entityManager->flush();
            $this->addFlash('success', 'The residence has been deleted.');
        }

        return $this->redirectToRoute('residence_index');
    }
}
