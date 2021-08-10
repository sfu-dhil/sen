<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
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
 * @Route("/event")
 */
class EventController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="event_index", methods={"GET"})
     *
     * @Template
     */
    public function index(Request $request, EventRepository $eventRepository) : array {
        $query = $eventRepository->indexQuery();
        $pageSize = (int) $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'events' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/typeahead", name="event_typeahead", methods={"GET"})
     */
    public function typeahead(Request $request, EventRepository $eventRepository) : JsonResponse {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];
        foreach ($eventRepository->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="event_new", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);

            foreach($event->getWitnesses() as $witness) {
                $witness->setEvent($event);
                $entityManager->persist($witness);
            }

            $entityManager->flush();
            $this->addFlash('success', 'The new event has been saved.');

            return $this->redirectToRoute('event_show', ['id' => $event->getId()]);
        }

        return [
            'event' => $event,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new_popup", name="event_new_popup", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request) {
        return $this->new($request);
    }

    /**
     * @Route("/{id}", name="event_show", methods={"GET"})
     * @Template
     */
    public function show(Event $event) : array {
        return [
            'event' => $event,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="event_edit", methods={"GET", "POST"})
     *
     * @Template
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, Event $event) {
        $witnesses = new ArrayCollection();
        foreach($event->getWitnesses() as $w) {
            $witnesses->add($w);
        }

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // delete any event witnesses that were removed in the form.
            foreach($witnesses as $w) {
                if( ! $event->getWitnesses()->contains($w)) {
                    $entityManager->remove($w);
                }
            }

            // Add any new event witnesses added in the form.
            foreach($event->getWitnesses() as $w) {
                if( ! $witnesses->contains($w)) {
                    $w->setEvent($event);
                    $entityManager->persist($w);
                }
            }

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated event has been saved.');

            return $this->redirectToRoute('event_show', ['id' => $event->getId()]);
        }

        return [
            'event' => $event,
            'form' => $form->createView(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="event_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Event $event) : RedirectResponse {
        if ($this->isCsrfTokenValid('delete' . $event->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($event);
            $entityManager->flush();
            $this->addFlash('success', 'The event has been deleted.');
        }

        return $this->redirectToRoute('event_index');
    }
}
