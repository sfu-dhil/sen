<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210616212440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE birth_status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, label VARCHAR(120) NOT NULL, description LONGTEXT DEFAULT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', FULLTEXT INDEX IDX_D787BDD6EA750E8 (label), FULLTEXT INDEX IDX_D787BDD66DE44026 (description), FULLTEXT INDEX IDX_D787BDD6EA750E86DE44026 (label, description), UNIQUE INDEX UNIQ_D787BDD65E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176B4BB6BBC');
        $this->addSql('DROP INDEX IDX_34DCD176B4BB6BBC ON person');
        $this->addSql('ALTER TABLE person ADD aliases LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', ADD occupations LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD statuses LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', ADD written_races LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', ADD notes LONGTEXT DEFAULT NULL, DROP occupation, DROP birth_date, DROP written_birth_date, DROP birth_status, DROP status, DROP title, CHANGE first_name first_name VARCHAR(255) DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL, CHANGE birth_place_id birth_status_id INT DEFAULT NULL, CHANGE alias titles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176CDF24A13 FOREIGN KEY (birth_status_id) REFERENCES birth_status (id)');
        $this->addSql('CREATE INDEX IDX_34DCD176CDF24A13 ON person (birth_status_id)');
        $this->addSql('ALTER TABLE residence ADD address VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE transactions CHANGE second_party_id second_party_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException();
    }
}
