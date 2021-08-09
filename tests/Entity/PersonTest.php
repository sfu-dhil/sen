<?php


namespace App\Tests\Entity;


use App\Entity\Person;
use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase {

    /**
     * @dataProvider nameData
     */
    public function testSetFirstName($name, $expected) {
        $person = new Person();
        $person->setFirstName($name);
        $this->assertEquals($expected, $person->getFirstName());
    }

    /**
     * @dataProvider nameData
     */
    public function testSetLastName($name, $expected) {
        $person = new Person();
        $person->setLastName($name);
        $this->assertEquals($expected, $person->getLastName());
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
