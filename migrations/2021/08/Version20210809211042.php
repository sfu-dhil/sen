<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210809211042 extends AbstractMigration {
    public function getDescription() : string {
        return 'Change empty strings to nulls  in person names.';
    }

    public function up(Schema $schema) : void {
        $this->addSql("update person set last_name=null where last_name rlike '^[[:space:]]*$'");
        $this->addSql("update person set first_name=null where first_name rlike '^[[:space:]]*$'");
    }

    public function down(Schema $schema) : void {
    }
}
