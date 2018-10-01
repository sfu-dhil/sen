<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EventCategory
 *
 * @ORM\Table(name="event_category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EventCategoryRepository")
 */
class EventCategory extends \Nines\UtilBundle\Entity\AbstractTerm
{
    /**
     * @var Collection|Event[]
     * @ORM\OneToMany(targetEntity="Event", mappedBy="category")
     */
    private $events;

}
