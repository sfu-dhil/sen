<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Menu;

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
    public const CARET = ' ▾';

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
     *
     * @return bool
     */
    private function hasRole($role) {
        if ( ! $this->tokenStorage->getToken()) {
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
        $menu->setChildrenAttributes([
            'class' => 'nav navbar-nav',
        ]);
        $menu->addChild('home', [
            'label' => 'Home',
            'route' => 'homepage',
        ]);

        $browse = $menu->addChild('browse', [
            'uri' => '#',
            'label' => 'Browse ' . self::CARET,
        ]);
        $browse->setAttribute('dropdown', true);
        $browse->setLinkAttribute('class', 'dropdown-toggle');
        $browse->setLinkAttribute('data-toggle', 'dropdown');
        $browse->setChildrenAttribute('class', 'dropdown-menu');

        $browse->addChild('Cities', [
            'route' => 'city_index',
        ]);
        $browse->addChild('Events', [
            'route' => 'event_index',
        ]);
        $browse->addChild('Ledgers', [
            'route' => 'ledger_index',
        ]);
        $browse->addChild('Locations', [
            'route' => 'location_index',
        ]);
        $browse->addChild('Notaries', [
            'route' => 'notary_index',
        ]);
        $browse->addChild('People', [
            'route' => 'person_index',
        ]);
        $browse->addChild('Races', [
            'route' => 'race_index',
        ]);
        $browse->addChild('Relationships', [
            'route' => 'relationship_index',
        ]);
        $browse->addChild('Residences', [
            'route' => 'residence_index',
        ]);
        $browse->addChild('Transactions', [
            'route' => 'transaction_index',
        ]);
        $browse->addChild('Witnesses', [
            'route' => 'witness_index',
        ]);

        if ($this->hasRole('ROLE_USER')) {
            $divider = $browse->addChild('divider', [
                'label' => '',
            ]);
            $divider->setAttributes([
                'role' => 'separator',
                'class' => 'divider',
            ]);

            $browse->addChild('Event Categories', [
                'route' => 'event_category_index',
            ]);
            $browse->addChild('Location Categories', [
                'route' => 'location_category_index',
            ]);
            $browse->addChild('Relationship Categories', [
                'route' => 'relationship_category_index',
            ]);
            $browse->addChild('Transaction Categories', [
                'route' => 'transaction_category_index',
            ]);
            $browse->addChild('Witness Categories', [
                'route' => 'witness_category_index',
            ]);
        }

        return $menu;
    }
}
