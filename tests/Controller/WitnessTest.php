<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Controller;

use App\DataFixtures\WitnessFixtures;
use App\Repository\WitnessRepository;
use Nines\UserBundle\DataFixtures\UserFixtures;
use Nines\UtilBundle\Tests\ControllerBaseCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 * @coversNothing
 */
class WitnessTest extends ControllerBaseCase {
    // Change this to HTTP_OK when the site is public.
    private const ANON_RESPONSE_CODE = Response::HTTP_FOUND;

    protected function fixtures() : array {
        return [
            WitnessFixtures::class,
            UserFixtures::class,
        ];
    }

    /**
     * @group anon
     * @group index
     */
    public function testAnonIndex() : void {
        $crawler = $this->client->request('GET', '/witness/');
        static::assertSame(self::ANON_RESPONSE_CODE, $this->client->getResponse()->getStatusCode());
        static::assertSame(0, $crawler->selectLink('New')->count());
    }

    /**
     * @group index
     * @group user
     */
    public function testUserIndex() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/witness/');
        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame(0, $crawler->selectLink('New')->count());
    }

    /**
     * @group admin
     * @group index
     */
    public function testAdminIndex() : void {
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/witness/');
        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->selectLink('New')->count());
    }

    /**
     * @group anon
     * @group show
     */
    public function testAnonShow() : void {
        $crawler = $this->client->request('GET', '/witness/1');
        static::assertSame(self::ANON_RESPONSE_CODE, $this->client->getResponse()->getStatusCode());
        static::assertSame(0, $crawler->selectLink('Edit')->count());
    }

    /**
     * @group show
     * @group user
     */
    public function testUserShow() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/witness/1');
        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame(0, $crawler->selectLink('Edit')->count());
    }

    /**
     * @group admin
     * @group show
     */
    public function testAdminShow() : void {
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/witness/1');
        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->selectLink('Edit')->count());
    }

    /**
     * @group anon
     * @group edit
     */
    public function testAnonEdit() : void {
        $crawler = $this->client->request('GET', '/witness/1/edit');
        static::assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        static::assertTrue($this->client->getResponse()->isRedirect());
    }

    /**
     * @group edit
     * @group user
     */
    public function testUserEdit() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/witness/1/edit');
        static::assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group admin
     * @group edit
     */
    public function testAdminEdit() : void {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/witness/1/edit');
        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Save')->form([
        ]);
        $form['witness[category]']->disableValidation()->setValue(1);
        $form['witness[person]']->disableValidation()->setValue(1);
        $form['witness[event]']->disableValidation()->setValue(1);

        $this->client->submit($form);
        static::assertTrue($this->client->getResponse()->isRedirect('/witness/1'));
        $responseCrawler = $this->client->followRedirect();
        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group anon
     * @group new
     */
    public function testAnonNew() : void {
        $crawler = $this->client->request('GET', '/witness/new');
        static::assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        static::assertTrue($this->client->getResponse()->isRedirect());
    }

    /**
     * @group anon
     * @group new
     */
    public function testAnonNewPopup() : void {
        $crawler = $this->client->request('GET', '/witness/new_popup');
        static::assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        static::assertTrue($this->client->getResponse()->isRedirect());
    }

    /**
     * @group new
     * @group user
     */
    public function testUserNew() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/witness/new');
        static::assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group new
     * @group user
     */
    public function testUserNewPopup() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/witness/new_popup');
        static::assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group admin
     * @group new
     */
    public function testAdminNew() : void {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/witness/new');
        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Save')->form([
        ]);
        $form['witness[category]']->disableValidation()->setValue(1);
        $form['witness[person]']->disableValidation()->setValue(1);
        $form['witness[event]']->disableValidation()->setValue(1);

        $this->client->submit($form);
        static::assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group admin
     * @group new
     */
    public function testAdminNewPopup() : void {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/witness/new_popup');
        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Save')->form([
        ]);
        $form['witness[category]']->disableValidation()->setValue(1);
        $form['witness[person]']->disableValidation()->setValue(1);
        $form['witness[event]']->disableValidation()->setValue(1);

        $this->client->submit($form);
        static::assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group admin
     * @group delete
     */
    public function testAdminDelete() : void {
        $repo = self::$container->get(WitnessRepository::class);
        $preCount = count($repo->findAll());

        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/witness/1');
        $form = $crawler->selectButton('Delete')->form();
        $this->client->submit($form);

        static::assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        static::assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $postCount = count($repo->findAll());
        static::assertSame($preCount - 1, $postCount);
    }
}
