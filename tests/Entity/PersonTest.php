<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Entity;

use App\Entity\Person;
use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase {
    /**
     * @dataProvider nameData
     *
     * @param mixed $name
     * @param mixed $expected
     *
     * @test
     */
    public function setFirstName($name, $expected) : void {
        $person = new Person();
        $person->setFirstName($name);
        $this->assertSame($expected, $person->getFirstName());
    }

    /**
     * @dataProvider nameData
     *
     * @param mixed $name
     * @param mixed $expected
     *
     * @test
     */
    public function setLastName($name, $expected) : void {
        $person = new Person();
        $person->setLastName($name);
        $this->assertSame($expected, $person->getLastName());
    }

    public function nameData() {
        return [
            ['John', 'John'],
            ['', null],
            [' ', null],
            ["\t", null],
            ["\n", null],
            ['  John', 'John'],
            ['John  ', 'John'],
            ['  John  ', 'John'],
            ['La Blanc', 'La Blanc'],
            [null, null],
        ];
    }
}
