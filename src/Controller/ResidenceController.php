<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Residence;
use App\Form\ResidenceType;
use App\Repository\ResidenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Residence controller.
 *
 * @Route("/residence")
 */
class ResidenceController extends AbstractController implements PaginatorAwareInterface
{
    use PaginatorTrait;

    /**
     * Lists all Residence entities.
     *
     * @return array
     *
     * @Route("/", name="residence_index", methods={"GET"})
     *
     * @Template
     */
    public function indexAction(Request $request, EntityManagerInterface $em) {
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Residence::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();

        $residences = $this->paginator->paginate($query, $request->query->getint('page', 1), 25);

        return [
            'residences' => $residences,
        ];
    }

    /**
     * Search for Residence entities.
     *
     * To make this work, add a method like this one to the
     * App:Residence repository. Replace the fieldName with
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
     * @Route("/search", name="residence_search", methods={"GET"})
     *
     * @Template
     */
    public function searchAction(Request $request, ResidenceRepository $repo) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);

            $residences = $this->paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $residences = [];
        }

        return [
            'residences' => $residences,
            'q' => $q,
        ];
    }

    /**
     * Creates a new Residence entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="residence_new", methods={"GET", "POST"})
     *
     * @Template
     */
    public function newAction(Request $request, EntityManagerInterface $em) {
        $residence = new Residence();
        $form = $this->createForm(ResidenceType::class, $residence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($residence);
            $em->flush();

            $this->addFlash('success', 'The new residence was created.');

            return $this->redirectToRoute('residence_show', ['id' => $residence->getId()]);
        }

        return [
            'residence' => $residence,
            'form' => $form->createView(),
        ];
    }

    /**
     * Creates a new Residence entity in a popup.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="residence_new_popup", methods={"GET", "POST"})
     *
     * @Template
     */
    public function newPopupAction(Request $request, EntityManagerInterface $em) {
        return $this->newAction($request, $em);
    }

    /**
     * Finds and displays a Residence entity.
     *
     * @return array
     *
     * @Route("/{id}", name="residence_show", methods={"GET"})
     *
     * @Template
     */
    public function showAction(Residence $residence) {
        return [
            'residence' => $residence,
        ];
    }

    /**
     * Displays a form to edit an existing Residence entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="residence_edit", methods={"GET", "POST"})
     *
     * @Template
     */
    public function editAction(Request $request, EntityManagerInterface $em, Residence $residence) {
        $editForm = $this->createForm(ResidenceType::class, $residence);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The residence has been updated.');

            return $this->redirectToRoute('residence_show', ['id' => $residence->getId()]);
        }

        return [
            'residence' => $residence,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Deletes a Residence entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="residence_delete", methods={"GET"})
     */
    public function deleteAction(Request $request, EntityManagerInterface $em, Residence $residence) {
        $em->remove($residence);
        $em->flush();
        $this->addFlash('success', 'The residence was deleted.');

        return $this->redirectToRoute('residence_index');
    }
}
