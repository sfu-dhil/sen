<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Person;
use Nines\UtilBundle\Tests\Util\BaseTestCase;

class PersonTest extends BaseTestCase {
    
    /**
     * @dataProvider setLastNameData
     */
    public function testSetLastName($expected, $name) {
        $person = new Person();
        $person->setLastName($name);
        $this->assertEquals($expected, $person->getLastName());
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
