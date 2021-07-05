<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210705172757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE transactions DROP FOREIGN KEY `FK_EAA81A4C12284F3C`, DROP FOREIGN KEY `FK_EAA81A4C95242202`');
        $this->addSql('ALTER TABLE transactions CHANGE COLUMN `first_party_id` `first_party_id` INT(11) NULL ;');
        $this->addSql('ALTER TABLE transactions ADD CONSTRAINT `FK_EAA81A4C12284F3C` FOREIGN KEY (`second_party_id`) REFERENCES person (`id`)  ON DELETE SET NULL, ADD CONSTRAINT `FK_EAA81A4C95242202` FOREIGN KEY (`first_party_id`) REFERENCES person (`id`) ON DELETE SET NULL;');
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException();
    }
}
