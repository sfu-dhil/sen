<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Menu builder for the navigation and search menus.
 */
class Builder implements ContainerAwareInterface {

    use ContainerAwareTrait;

    // U+25BE, black down-pointing small triangle.
    const CARET = ' ▾';

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authChecker;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Build the menu builder.
     *
     * @param FactoryInterface $factory
     * @param AuthorizationCheckerInterface $authChecker
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authChecker, TokenStorageInterface $tokenStorage) {
        $this->factory = $factory;
        $this->authChecker = $authChecker;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Check if the current user has the given role.
     *
     * @param string $role
     * @return boolean
     */
    private function hasRole($role) {
        if (!$this->tokenStorage->getToken()) {
            return false;
        }
        return $this->authChecker->isGranted($role);
    }

    /**
     * Build the navigation menu and return it.
     *
     * @return ItemInterface
     */
    public function mainMenu() {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes(array(
            'class' => 'nav navbar-nav',
        ));
        $menu->addChild('home', array(
            'label' => 'Home',
            'route' => 'homepage',
        ));

        $browse = $menu->addChild('browse', array(
            'uri' => '#',
            'label' => 'Browse ' . self::CARET,
        ));
        $browse->setAttribute('dropdown', true);
        $browse->setLinkAttribute('class', 'dropdown-toggle');
        $browse->setLinkAttribute('data-toggle', 'dropdown');
        $browse->setChildrenAttribute('class', 'dropdown-menu');

        $browse->addChild('Cities', array(
            'route' => 'city_index',
        ));
        $browse->addChild('Events', array(
            'route' => 'event_index',
        ));
        $browse->addChild('Ledgers', array(
            'route' => 'ledger_index',
        ));
        $browse->addChild('Locations', array(
            'route' => 'location_index',
        ));
        $browse->addChild('Notaries', array(
            'route' => 'notary_index',
        ));
        $browse->addChild('People', array(
            'route' => 'person_index',
        ));
        $browse->addChild('Races', array(
            'route' => 'race_index',
        ));
        $browse->addChild('Relationships', array(
            'route' => 'relationship_index',
        ));
        $browse->addChild('Residences', array(
            'route' => 'residence_index',
        ));
        $browse->addChild('Transactions', array(
            'route' => 'transaction_index',
        ));
        $browse->addChild('Witnesses', array(
            'route' => 'witness_index',
        ));

        if ($this->hasRole('ROLE_USER')) {
            $divider = $browse->addChild('divider', array(
                'label' => '',
            ));
            $divider->setAttributes(array(
                'role' => 'separator',
                'class' => 'divider',
            ));

            $browse->addChild('Event Categories', array(
                'route' => 'event_category_index',
            ));
            $browse->addChild('Location Categories', array(
                'route' => 'location_category_index',
            ));
            $browse->addChild('Relationship Categories', array(
                'route' => 'relationship_category_index',
            ));
            $browse->addChild('Transaction Categories', array(
                'route' => 'transaction_category_index',
            ));
            $browse->addChild('Witness Categories', array(
                'route' => 'witness_category_index',
            ));
        }

        return $menu;
    }

}
