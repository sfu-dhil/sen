<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Entity;

use App\Entity\Person;
use Nines\UtilBundle\Tests\ControllerBaseCase;

/**
 * @internal
 * @coversNothing
 */
class PersonTest extends ControllerBaseCase {
    /**
     * @dataProvider setLastNameData
     *
     * @param mixed $expected
     * @param mixed $name
     */
    public function testSetLastName($expected, $name) : void {
        $person = new Person();
        $person->setLastName($name);
        static::assertSame($expected, $person->getLastName());
    }

    public function setLastNameData() {
        return [
            ['Charlie', 'Charlie'],
            ['Björk', 'Björk'],
            ['Guðmundsdóttir', 'Guðmundsdóttir'],
            ['毛', '毛'],
            ['Carreño', 'Carreño'],
            ['Борис', 'Борис'],
            ['แม้ว', 'แม้ว'],

            ['Charlie', 'CHARLIE'],
            ['Björk', 'BJÖRK'],
            ['Guðmundsdóttir', 'GUÐMUNDSDÓTTIR'],
            ['Carreño', 'CARREÑO'],
            ['Борис', 'БОРИС'],
        ];
    }
}
