<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Controller;

use App\DataFixtures\PersonFixtures;
use App\Repository\PersonRepository;
use Nines\UserBundle\DataFixtures\UserFixtures;
use Nines\UtilBundle\Tests\ControllerBaseCase;
use Symfony\Component\HttpFoundation\Response;

class PersonTest extends ControllerBaseCase {
    // Change this to HTTP_OK when the site is public.
    private const ANON_RESPONSE_CODE = Response::HTTP_FOUND;

    private const TYPEAHEAD_QUERY = 'lastname';

    protected function fixtures() : array {
        return [
            PersonFixtures::class,
            UserFixtures::class,
        ];
    }

    /**
     * @group anon
     * @group index
     */
    public function testAnonIndex() : void {
        $crawler = $this->client->request('GET', '/person/');
        $this->assertSame(self::ANON_RESPONSE_CODE, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    /**
     * @group user
     * @group index
     */
    public function testUserIndex() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/person/');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    /**
     * @group admin
     * @group index
     */
    public function testAdminIndex() : void {
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/person/');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('New')->count());
    }

    /**
     * @group anon
     * @group show
     */
    public function testAnonShow() : void {
        $crawler = $this->client->request('GET', '/person/1');
        $this->assertSame(self::ANON_RESPONSE_CODE, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
    }

    /**
     * @group user
     * @group show
     */
    public function testUserShow() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/person/1');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
    }

    /**
     * @group admin
     * @group show
     */
    public function testAdminShow() : void {
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/person/1');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('Edit')->count());
    }

    /**
     * @group anon
     * @group typeahead
     */
    public function testAnonTypeahead() : void {
        $this->client->request('GET', '/person/typeahead?q=' . self::TYPEAHEAD_QUERY);
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
     * @group user
     * @group typeahead
     */
    public function testUserTypeahead() : void {
        $this->login('user.user');
        $this->client->request('GET', '/person/typeahead?q=' . self::TYPEAHEAD_QUERY);
        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('content-type'));
        $json = json_decode($response->getContent());
        $this->assertCount(4, $json);
    }

    /**
     * @group admin
     * @group typeahead
     */
    public function testAdminTypeahead() : void {
        $this->login('user.admin');
        $this->client->request('GET', '/person/typeahead?q=' . self::TYPEAHEAD_QUERY);
        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('content-type'));
        $json = json_decode($response->getContent());
        $this->assertCount(4, $json);
    }

    public function testAnonSearch() : void {
        $repo = $this->createMock(PersonRepository::class);
        $repo->method('searchQuery')->willReturn([$this->getReference('person.1')]);
        $this->client->disableReboot();
        $this->client->getContainer()->set('test.' . PersonRepository::class, $repo);

        $crawler = $this->client->request('GET', '/person/search');
        $this->assertSame(self::ANON_RESPONSE_CODE, $this->client->getResponse()->getStatusCode());
        if (self::ANON_RESPONSE_CODE === Response::HTTP_FOUND) {
            // If authentication is required stop here.
            return;
        }

        $form = $crawler->selectButton('btn-search')->form([
            'q' => 'person',
        ]);

        $responseCrawler = $this->client->submit($form);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    public function testUserSearch() : void {
        $repo = $this->createMock(PersonRepository::class);
        $repo->method('searchQuery')->willReturn([$this->getReference('person.1')]);
        $this->client->disableReboot();
        $this->client->getContainer()->set('test.' . PersonRepository::class, $repo);

        $this->login('user.user');
        $crawler = $this->client->request('GET', '/person/search');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('btn-search')->form([
            'q' => 'person',
        ]);

        $responseCrawler = $this->client->submit($form);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminSearch() : void {
        $repo = $this->createMock(PersonRepository::class);
        $repo->method('searchQuery')->willReturn([$this->getReference('person.1')]);
        $this->client->disableReboot();
        $this->client->getContainer()->set('test.' . PersonRepository::class, $repo);

        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/person/search');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('btn-search')->form([
            'q' => 'person',
        ]);

        $responseCrawler = $this->client->submit($form);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group anon
     * @group edit
     */
    public function testAnonEdit() : void {
        $crawler = $this->client->request('GET', '/person/1/edit');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    /**
     * @group user
     * @group edit
     */
    public function testUserEdit() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/person/1/edit');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group admin
     * @group edit
     */
    public function testAdminEdit() : void {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/person/1/edit');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Save')->form([
            'person[firstName]' => 'Updated FirstName',
            'person[lastName]' => 'Updated LastName',
            'person[alias]' => ['Updated Alias'],
            'person[native]' => 'Updated Native',
            'person[occupation]' => ['Updated Occupation'],
            'person[sex]' => 'Updated Sex',
            'person[birthDate]' => 'Updated BirthDate',
            'person[writtenBirthDate]' => 'Updated WrittenBirthDate',
            'person[birthStatus]' => 'Updated BirthStatus',
            'person[status]' => 'Updated Status',
        ]);

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect('/person/1'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Updated FirstName")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Updated LastName")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Updated Alias")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Updated Native")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Updated Occupation")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Updated Sex")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Updated BirthDate")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Updated WrittenBirthDate")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Updated BirthStatus")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Updated Status")')->count());
    }

    /**
     * @group anon
     * @group new
     */
    public function testAnonNew() : void {
        $crawler = $this->client->request('GET', '/person/new');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    /**
     * @group anon
     * @group new
     */
    public function testAnonNewPopup() : void {
        $crawler = $this->client->request('GET', '/person/new_popup');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    /**
     * @group user
     * @group new
     */
    public function testUserNew() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/person/new');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group user
     * @group new
     */
    public function testUserNewPopup() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/person/new_popup');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group admin
     * @group new
     */
    public function testAdminNew() : void {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/person/new');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Save')->form([
            'person[firstName]' => 'New FirstName',
            'person[lastName]' => 'New LastName',
            'person[native]' => 'New Native',
            'person[sex]' => 'New Sex',
            'person[birthDate]' => 'New BirthDate',
            'person[writtenBirthDate]' => 'New WrittenBirthDate',
            'person[birthStatus]' => 'New BirthStatus',
            'person[status]' => 'New Status',
        ]);
        $values = $form->getPhpValues();
        $values['person']['alias'][0] = 'New Alias';
        $values['person']['occupation'][0] = 'New Occupation';
        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New FirstName")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New LastName")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New Alias")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New Native")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New Occupation")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New Sex")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New BirthDate")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New WrittenBirthDate")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New BirthStatus")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New Status")')->count());
    }

    /**
     * @group admin
     * @group new
     */
    public function testAdminNewPopup() : void {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/person/new_popup');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Save')->form([
            'person[firstName]' => 'New FirstName',
            'person[lastName]' => 'New LastName',
            'person[native]' => 'New Native',
            'person[sex]' => 'New Sex',
            'person[birthDate]' => 'New BirthDate',
            'person[writtenBirthDate]' => 'New WrittenBirthDate',
            'person[birthStatus]' => 'New BirthStatus',
            'person[status]' => 'New Status',
        ]);
        $values = $form->getPhpValues();
        $values['person']['alias'][0] = 'New Alias';
        $values['person']['occupation'][0] = 'New Occupation';
        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New FirstName")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New LastName")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New Alias")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New Native")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New Occupation")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New Sex")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New BirthDate")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New WrittenBirthDate")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New BirthStatus")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New Status")')->count());
    }

    /**
     * @group admin
     * @group delete
     */
    public function testAdminDelete() : void {
        $repo = self::$container->get(PersonRepository::class);
        $preCount = count($repo->findAll());

        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/person/1');
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
