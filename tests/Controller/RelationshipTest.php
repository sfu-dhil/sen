<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Controller;

use App\DataFixtures\RelationshipFixtures;
use App\Repository\RelationshipRepository;
use Nines\UserBundle\DataFixtures\UserFixtures;
use Nines\UtilBundle\Tests\ControllerBaseCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
class RelationshipTest extends ControllerBaseCase {
    // Change this to HTTP_OK when the site is public.
    private const ANON_RESPONSE_CODE = Response::HTTP_FOUND;

    protected function fixtures() : array {
        return [
            RelationshipFixtures::class,
            UserFixtures::class,
        ];
    }

    /**
     * @group anon
     * @group index
     *
     * @test
     */
    public function anonIndex() : void {
        $crawler = $this->client->request('GET', '/relationship/');
        $this->assertSame(self::ANON_RESPONSE_CODE, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    /**
     * @group index
     * @group user
     *
     * @test
     */
    public function userIndex() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/relationship/');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    /**
     * @group admin
     * @group index
     *
     * @test
     */
    public function adminIndex() : void {
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/relationship/');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('New')->count());
    }

    /**
     * @group anon
     * @group show
     *
     * @test
     */
    public function anonShow() : void {
        $crawler = $this->client->request('GET', '/relationship/1');
        $this->assertSame(self::ANON_RESPONSE_CODE, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
    }

    /**
     * @group show
     * @group user
     *
     * @test
     */
    public function userShow() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/relationship/1');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
    }

    /**
     * @group admin
     * @group show
     *
     * @test
     */
    public function adminShow() : void {
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/relationship/1');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('Edit')->count());
    }

    /**
     * @group anon
     * @group edit
     *
     * @test
     */
    public function anonEdit() : void {
        $crawler = $this->client->request('GET', '/relationship/1/edit');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    /**
     * @group edit
     * @group user
     *
     * @test
     */
    public function userEdit() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/relationship/1/edit');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group admin
     * @group edit
     *
     * @test
     */
    public function adminEdit() : void {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/relationship/1/edit');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Save')->form([
            'relationship[startDate]' => 'Updated StartDate',
            'relationship[endDate]' => 'Updated EndDate',
        ]);
        $form['relationship[category]']->disableValidation()->setValue(1);
        $form['relationship[person]']->disableValidation()->setValue(2);
        $form['relationship[relation]']->disableValidation()->setValue(2);

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect('/relationship/1'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Updated StartDate")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Updated EndDate")')->count());
    }

    /**
     * @group anon
     * @group new
     *
     * @test
     */
    public function anonNew() : void {
        $crawler = $this->client->request('GET', '/relationship/new');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    /**
     * @group anon
     * @group new
     *
     * @test
     */
    public function anonNewPopup() : void {
        $crawler = $this->client->request('GET', '/relationship/new_popup');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    /**
     * @group new
     * @group user
     *
     * @test
     */
    public function userNew() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/relationship/new');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group new
     * @group user
     *
     * @test
     */
    public function userNewPopup() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/relationship/new_popup');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group admin
     * @group new
     *
     * @test
     */
    public function adminNew() : void {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/relationship/new');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Save')->form([
            'relationship[startDate]' => 'New StartDate',
            'relationship[endDate]' => 'New EndDate',
        ]);
        $form['relationship[category]']->disableValidation()->setValue(1);
        $form['relationship[person]']->disableValidation()->setValue(2);
        $form['relationship[relation]']->disableValidation()->setValue(2);

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New StartDate")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New EndDate")')->count());
    }

    /**
     * @group admin
     * @group new
     *
     * @test
     */
    public function adminNewPopup() : void {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/relationship/new_popup');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Save')->form([
            'relationship[startDate]' => 'New StartDate',
            'relationship[endDate]' => 'New EndDate',
        ]);
        $form['relationship[category]']->disableValidation()->setValue(1);
        $form['relationship[person]']->disableValidation()->setValue(2);
        $form['relationship[relation]']->disableValidation()->setValue(2);

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New StartDate")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New EndDate")')->count());
    }

    /**
     * @group admin
     * @group delete
     *
     * @test
     */
    public function adminDelete() : void {
        $repo = self::$container->get(RelationshipRepository::class);
        $preCount = count($repo->findAll());

        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/relationship/1');
        $form = $crawler->selectButton('Delete')->form();
        $this->client->submit($form);

        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $postCount = count($repo->findAll());
        $this->assertSame($preCount - 1, $postCount);
    }
}
