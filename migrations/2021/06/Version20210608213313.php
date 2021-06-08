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
final class Version20210608213313 extends AbstractMigration {
    public function getDescription() : string {
        return '';
    }

    public function up(Schema $schema) : void {
        $this->addSql('UPDATE person SET titles = \'a:0:{}\' WHERE titles IS NULL or titles = \'\'');
        $this->addSql('UPDATE person SET aliases = \'a:0:{}\' WHERE aliases IS NULL or aliases = \'\'');
        $this->addSql('UPDATE person SET occupations = \'[]\' WHERE occupations IS NULL or occupations = \'\'');
        $this->addSql('UPDATE person SET statuses = \'a:0:{}\' WHERE statuses IS NULL or statuses = \'\'');
        $this->addSql('UPDATE person SET written_races = \'a:0:{}\' WHERE written_races IS NULL or written_races = \'\'');
        $alter = <<<'ENDALTER'
                ALTER TABLE `person`
                CHANGE COLUMN `titles` `titles` LONGTEXT NOT NULL DEFAULT 'a:0:{}' COMMENT '(DC2Type:array)' ,
                CHANGE COLUMN `aliases` `aliases` LONGTEXT NOT NULL DEFAULT 'a:0:{}' COMMENT '(DC2Type:array)' ,
                CHANGE COLUMN `occupations` `occupations` LONGTEXT NOT NULL DEFAULT '[]' COMMENT '(DC2Type:json)' ,
                CHANGE COLUMN `statuses` `statuses` LONGTEXT NOT NULL DEFAULT 'a:0:{}' COMMENT '(DC2Type:array)' ,
                CHANGE COLUMN `written_races` `written_races` LONGTEXT NOT NULL DEFAULT 'a:0:{}' COMMENT '(DC2Type:array)' ;
            ENDALTER;
        $this->addSql($alter);
    }

    public function down(Schema $schema) : void {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
