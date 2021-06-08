<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Race;
use App\Form\RaceType;
use App\Repository\RaceRepository;
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
 * @Route("/race")
 */
class RaceController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="race_index", methods={"GET"})
     *
     * @Template
     */
    public function index(Request $request, RaceRepository $raceRepository) : array {
        $query = $raceRepository->indexQuery();
        $pageSize = (int) $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'races' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/search", name="race_search", methods={"GET"})
     *
     * @Template
     */
    public function search(Request $request, RaceRepository $raceRepository) : array {
        $q = $request->query->get('q');
        if ($q) {
            $query = $raceRepository->searchQuery($q);
            $races = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);
        } else {
            $races = [];
        }

        return [
            'races' => $races,
            'q' => $q,
        ];
    }

    /**
     * @Route("/typeahead", name="race_typeahead", methods={"GET"})
     */
    public function typeahead(Request $request, RaceRepository $raceRepository) : JsonResponse {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];
        foreach ($raceRepository->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="race_new", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $race = new Race();
        $form = $this->createForm(RaceType::class, $race);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($race);
            $entityManager->flush();
            $this->addFlash('success', 'The new race has been saved.');

            return $this->redirectToRoute('race_show', ['id' => $race->getId()]);
        }

        return [
            'race' => $race,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new_popup", name="race_new_popup", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request) {
        return $this->new($request);
    }

    /**
     * @Route("/{id}", name="race_show", methods={"GET"})
     * @Template
     */
    public function show(Race $race) : array {
        return [
            'race' => $race,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="race_edit", methods={"GET", "POST"})
     *
     * @Template
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, Race $race) {
        $form = $this->createForm(RaceType::class, $race);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated race has been saved.');

            return $this->redirectToRoute('race_show', ['id' => $race->getId()]);
        }

        return [
            'race' => $race,
            'form' => $form->createView(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="race_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Race $race) : RedirectResponse {
        if ($this->isCsrfTokenValid('delete' . $race->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($race);
            $entityManager->flush();
            $this->addFlash('success', 'The race has been deleted.');
        }

        return $this->redirectToRoute('race_index');
    }
}
