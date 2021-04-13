<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Person;
use App\Form\PersonType;
use App\Repository\PersonRepository;
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
 * Person controller.
 *
 * @Route("/person")
 */
class PersonController extends AbstractController implements PaginatorAwareInterface
{
    use PaginatorTrait;

    /**
     * Lists all Person entities.
     *
     * @return array
     *
     * @Route("/", name="person_index", methods={"GET"})
     *
     * @Template
     */
    public function indexAction(Request $request, EntityManagerInterface $em) {
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Person::class, 'e')->orderBy('e.lastName', 'ASC');
        $query = $qb->getQuery();

        $people = $this->paginator->paginate($query, $request->query->getint('page', 1), 25);

        return [
            'people' => $people,
        ];
    }

    /**
     * Typeahead API endpoint for Person entities.
     *
     * @Route("/typeahead", name="person_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, PersonRepository $repo) {
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
     * Search for Person entities.
     *
     * To make this work, add a method like this one to the
     * App:Person repository. Replace the fieldName with
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
     * @Route("/search", name="person_search", methods={"GET"})
     *
     * @Template
     */
    public function searchAction(Request $request, PersonRepository $repo) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);

            $people = $this->paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $people = [];
        }

        return [
            'people' => $people,
            'q' => $q,
        ];
    }

    /**
     * Creates a new Person entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="person_new", methods={"GET", "POST"})
     *
     * @Template
     */
    public function newAction(Request $request, EntityManagerInterface $em) {
        $person = new Person();
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($person);
            $em->flush();

            $this->addFlash('success', 'The new person was created.');

            return $this->redirectToRoute('person_show', ['id' => $person->getId()]);
        }

        return [
            'person' => $person,
            'form' => $form->createView(),
        ];
    }

    /**
     * Creates a new Person entity in a popup.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="person_new_popup", methods={"GET", "POST"})
     *
     * @Template
     */
    public function newPopupAction(Request $request, EntityManagerInterface $em) {
        return $this->newAction($request, $em);
    }

    /**
     * Finds and displays a Person entity.
     *
     * @return array
     *
     * @Route("/{id}", name="person_show", methods={"GET"})
     *
     * @Template
     */
    public function showAction(Person $person) {
        return [
            'person' => $person,
        ];
    }

    /**
     * Displays a form to edit an existing Person entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="person_edit", methods={"GET", "POST"})
     *
     * @Template
     */
    public function editAction(Request $request, EntityManagerInterface $em, Person $person) {
        $editForm = $this->createForm(PersonType::class, $person);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The person has been updated.');

            return $this->redirectToRoute('person_show', ['id' => $person->getId()]);
        }

        return [
            'person' => $person,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Deletes a Person entity.
     *
     * @return array|RedirectResponse
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="person_delete", methods={"GET"})
     */
    public function deleteAction(Request $request, EntityManagerInterface $em, Person $person) {
        $em->remove($person);
        $em->flush();
        $this->addFlash('success', 'The person was deleted.');

        return $this->redirectToRoute('person_index');
    }
}
