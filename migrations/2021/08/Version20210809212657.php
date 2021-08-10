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
final class Version20210809212657 extends AbstractMigration {
    private const weights = [
        'father', 'mother', 'spouse', 'child', 'brother', 'sister',
        'godfather', 'godmother', 'godchild',
    ];

    public function getDescription() : string {
        return '';
    }

    public function up(Schema $schema) : void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE relationship_category ADD weight INT DEFAULT 0');
        foreach (self::weights as $w => $label) {
            $this->addSql("UPDATE relationship_category SET weight={$w} WHERE label = '{$label}'");
        }
    }

    public function down(Schema $schema) : void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE relationship_category DROP weight');
    }
}
