<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Entity;

use App\Entity\Person;
use Nines\UtilBundle\Tests\ControllerBaseCase;

class PersonTest extends ControllerBaseCase
{
    /**
     * @dataProvider setLastNameData
     *
     * @param mixed $expected
     * @param mixed $name
     */
    public function testSetLastName($expected, $name) : void {
        $person = new Person();
        $person->setLastName($name);
        $this->assertSame($expected, $person->getLastName());
    }

    public function setLastNameData() {
        return [
            ['CHARLIE', 'Charlie'],
            ['BJÖRK', 'Björk'],
            ['GUÐMUNDSDÓTTIR', 'Guðmundsdóttir'],
            ['毛', '毛'],
            ['CARREÑO', 'Carreño'],
            ['БОРИС', 'Борис'],
            ['แม้ว', 'แม้ว'],
        ];
    }
}
