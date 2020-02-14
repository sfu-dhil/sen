<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Race;
use App\Form\RaceType;
use App\Repository\RaceRepository;
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
 * Race controller.
 *
 * @Route("/race")
 */
class RaceController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Lists all Race entities.
     *
     * @return array
     *
     * @Route("/", name="race_index", methods={"GET"})
     *
     * @Template()
     */
    public function indexAction(Request $request, EntityManagerInterface $em) {
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Race::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();

        $races = $this->paginator->paginate($query, $request->query->getint('page', 1), 25);

        return [
            'races' => $races,
        ];
    }

    /**
     * Typeahead API endpoint for Race entities.
     *
     * To make this work, add something like this to RaceRepository:
     *
     * @Route("/typeahead", name="race_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, RaceRepository $repo) {
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
     * Search for Race entities.
     *
     * To make this work, add a method like this one to the
     * App:Race repository. Replace the fieldName with
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
     * @Route("/search", name="race_search", methods={"GET"})
     *
     * @Template()
     */
    public function searchAction(Request $request, RaceRepository $repo) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);

            $races = $this->paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $races = [];
        }

        return [
            'races' => $races,
            'q' => $q,
        ];
    }

    /**
     * Creates a new Race entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="race_new", methods={"GET","POST"})
     *
     * @Template()
     */
    public function newAction(Request $request, EntityManagerInterface $em) {
        $race = new Race();
        $form = $this->createForm(RaceType::class, $race);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($race);
            $em->flush();

            $this->addFlash('success', 'The new race was created.');

            return $this->redirectToRoute('race_show', ['id' => $race->getId()]);
        }

        return [
            'race' => $race,
            'form' => $form->createView(),
        ];
    }

    /**
     * Creates a new Race entity in a popup.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="race_new_popup", methods={"GET","POST"})
     *
     * @Template()
     */
    public function newPopupAction(Request $request) {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a Race entity.
     *
     * @return array
     *
     * @Route("/{id}", name="race_show", methods={"GET"})
     *
     * @Template()
     */
    public function showAction(Race $race) {
        return [
            'race' => $race,
        ];
    }

    /**
     * Displays a form to edit an existing Race entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="race_edit", methods={"GET","POST"})
     *
     * @Template()
     */
    public function editAction(Request $request, EntityManagerInterface $em, Race $race) {
        $editForm = $this->createForm(RaceType::class, $race);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The race has been updated.');

            return $this->redirectToRoute('race_show', ['id' => $race->getId()]);
        }

        return [
            'race' => $race,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Deletes a Race entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="race_delete", methods={"GET"})
     */
    public function deleteAction(Request $request, EntityManagerInterface $em, Race $race) {
        $em->remove($race);
        $em->flush();
        $this->addFlash('success', 'The race was deleted.');

        return $this->redirectToRoute('race_index');
    }
}
