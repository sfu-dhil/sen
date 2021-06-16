<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Controller;

use App\DataFixtures\CityFixtures;
use App\Repository\CityRepository;
use Nines\UserBundle\DataFixtures\UserFixtures;
use Nines\UtilBundle\Tests\ControllerBaseCase;
use Symfony\Component\HttpFoundation\Response;

class CityTest extends ControllerBaseCase {
    // Change this to HTTP_OK when the site is public.
    private const ANON_RESPONSE_CODE = Response::HTTP_FOUND;

    private const TYPEAHEAD_QUERY = 'name';

    protected function fixtures() : array {
        return [
            CityFixtures::class,
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
        $crawler = $this->client->request('GET', '/city/');
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
        $crawler = $this->client->request('GET', '/city/');
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
        $crawler = $this->client->request('GET', '/city/');
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
        $crawler = $this->client->request('GET', '/city/1');
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
        $crawler = $this->client->request('GET', '/city/1');
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
        $crawler = $this->client->request('GET', '/city/1');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('Edit')->count());
    }

    /**
     * @group anon
     * @group typeahead
     *
     * @test
     */
    public function anonTypeahead() : void {
        $this->client->request('GET', '/city/typeahead?q=' . self::TYPEAHEAD_QUERY);
        $response = $this->client->getResponse();
        $this->assertSame(self::ANON_RESPONSE_CODE, $this->client->getResponse()->getStatusCode());
        if (self::ANON_RESPONSE_CODE === Response::HTTP_FOUND) {
            // If authentication is required stop here.
            return;
        }
        $this->assertSame('application/json', $response->headers->get('content-type'));
        $json = json_decode($response->getContent());
        $this->assertCount(4, $json);
    }

    /**
     * @group typeahead
     * @group user
     *
     * @test
     */
    public function userTypeahead() : void {
        $this->login('user.user');
        $this->client->request('GET', '/city/typeahead?q=' . self::TYPEAHEAD_QUERY);
        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('content-type'));
        $json = json_decode($response->getContent());
        $this->assertCount(4, $json);
    }

    /**
     * @group admin
     * @group typeahead
     *
     * @test
     */
    public function adminTypeahead() : void {
        $this->login('user.admin');
        $this->client->request('GET', '/city/typeahead?q=' . self::TYPEAHEAD_QUERY);
        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('content-type'));
        $json = json_decode($response->getContent());
        $this->assertCount(4, $json);
    }

    /**
     * @test
     */
    public function anonSearch() : void {
        $repo = $this->createMock(CityRepository::class);
        $repo->method('searchQuery')->willReturn([$this->getReference('city.1')]);
        $this->client->disableReboot();
        $this->client->getContainer()->set('test.' . CityRepository::class, $repo);

        $crawler = $this->client->request('GET', '/city/search');
        $this->assertSame(self::ANON_RESPONSE_CODE, $this->client->getResponse()->getStatusCode());
        if (self::ANON_RESPONSE_CODE === Response::HTTP_FOUND) {
            // If authentication is required stop here.
            return;
        }

        $form = $crawler->selectButton('btn-search')->form([
            'q' => 'city',
        ]);

        $responseCrawler = $this->client->submit($form);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function userSearch() : void {
        $repo = $this->createMock(CityRepository::class);
        $repo->method('searchQuery')->willReturn([$this->getReference('city.1')]);
        $this->client->disableReboot();
        $this->client->getContainer()->set('test.' . CityRepository::class, $repo);

        $this->login('user.user');
        $crawler = $this->client->request('GET', '/city/search');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('btn-search')->form([
            'q' => 'city',
        ]);

        $responseCrawler = $this->client->submit($form);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function adminSearch() : void {
        $repo = $this->createMock(CityRepository::class);
        $repo->method('searchQuery')->willReturn([$this->getReference('city.1')]);
        $this->client->disableReboot();
        $this->client->getContainer()->set('test.' . CityRepository::class, $repo);

        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/city/search');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('btn-search')->form([
            'q' => 'city',
        ]);

        $responseCrawler = $this->client->submit($form);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group anon
     * @group edit
     *
     * @test
     */
    public function anonEdit() : void {
        $crawler = $this->client->request('GET', '/city/1/edit');
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
        $crawler = $this->client->request('GET', '/city/1/edit');
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
        $formCrawler = $this->client->request('GET', '/city/1/edit');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Save')->form([
            'city[name]' => 'Updated Name',
        ]);

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect('/city/1'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Updated Name")')->count());
    }

    /**
     * @group anon
     * @group new
     *
     * @test
     */
    public function anonNew() : void {
        $crawler = $this->client->request('GET', '/city/new');
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
        $crawler = $this->client->request('GET', '/city/new_popup');
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
        $crawler = $this->client->request('GET', '/city/new');
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
        $crawler = $this->client->request('GET', '/city/new_popup');
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
        $formCrawler = $this->client->request('GET', '/city/new');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Save')->form([
            'city[name]' => 'New Name',
        ]);

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New Name")')->count());
    }

    /**
     * @group admin
     * @group new
     *
     * @test
     */
    public function adminNewPopup() : void {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/city/new_popup');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Save')->form([
            'city[name]' => 'New Name',
        ]);

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New Name")')->count());
    }

    /**
     * @group admin
     * @group delete
     *
     * @test
     */
    public function adminDelete() : void {
        $repo = self::$container->get(CityRepository::class);
        $preCount = count($repo->findAll());

        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/city/1');
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
