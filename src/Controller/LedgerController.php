<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Ledger;
use App\Form\LedgerType;
use App\Repository\LedgerRepository;
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
 * Ledger controller.
 *
 * @Route("/ledger")
 */
class LedgerController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Lists all Ledger entities.
     *
     * @return array
     *
     * @Route("/", name="ledger_index", methods={"GET"})
     *
     * @Template
     */
    public function indexAction(Request $request, EntityManagerInterface $em) {
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Ledger::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();

        $ledgers = $this->paginator->paginate($query, $request->query->getint('page', 1), 25);

        return [
            'ledgers' => $ledgers,
        ];
    }

    /**
     * Typeahead API endpoint for Ledger entities.
     *
     * To make this work, add something like this to LedgerRepository:
     *
     * @Route("/typeahead", name="ledger_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, LedgerRepository $repo) {
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
     * Search for Ledger entities.
     *
     * To make this work, add a method like this one to the
     * App:Ledger repository. Replace the fieldName with
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
     * @Route("/search", name="ledger_search", methods={"GET"})
     *
     * @Template
     */
    public function searchAction(Request $request, LedgerRepository $repo) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);

            $ledgers = $this->paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $ledgers = [];
        }

        return [
            'ledgers' => $ledgers,
            'q' => $q,
        ];
    }

    /**
     * Creates a new Ledger entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="ledger_new", methods={"GET", "POST"})
     *
     * @Template
     */
    public function newAction(Request $request, EntityManagerInterface $em) {
        $ledger = new Ledger();
        $form = $this->createForm(LedgerType::class, $ledger);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ledger);
            $em->flush();

            $this->addFlash('success', 'The new ledger was created.');

            return $this->redirectToRoute('ledger_show', ['id' => $ledger->getId()]);
        }

        return [
            'ledger' => $ledger,
            'form' => $form->createView(),
        ];
    }

    /**
     * Creates a new Ledger entity in a popup.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="ledger_new_popup", methods={"GET", "POST"})
     *
     * @Template
     */
    public function newPopupAction(Request $request, EntityManagerInterface $em) {
        return $this->newAction($request, $em);
    }

    /**
     * Finds and displays a Ledger entity.
     *
     * @return array
     *
     * @Route("/{id}", name="ledger_show", methods={"GET"})
     *
     * @Template
     */
    public function showAction(Ledger $ledger) {
        return [
            'ledger' => $ledger,
        ];
    }

    /**
     * Displays a form to edit an existing Ledger entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="ledger_edit", methods={"GET", "POST"})
     *
     * @Template
     */
    public function editAction(Request $request, EntityManagerInterface $em, Ledger $ledger) {
        $editForm = $this->createForm(LedgerType::class, $ledger);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The ledger has been updated.');

            return $this->redirectToRoute('ledger_show', ['id' => $ledger->getId()]);
        }

        return [
            'ledger' => $ledger,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Deletes a Ledger entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="ledger_delete", methods={"GET"})
     */
    public function deleteAction(Request $request, EntityManagerInterface $em, Ledger $ledger) {
        $em->remove($ledger);
        $em->flush();
        $this->addFlash('success', 'The ledger was deleted.');

        return $this->redirectToRoute('ledger_index');
    }
}
