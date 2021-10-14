<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211014093026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client ADD country_id INT DEFAULT NULL, ADD city_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C74404558BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_C7440455F92F3E70 ON client (country_id)');
        $this->addSql('CREATE INDEX IDX_C74404558BAC62AF ON client (city_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455F92F3E70');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C74404558BAC62AF');
        $this->addSql('DROP INDEX IDX_C7440455F92F3E70 ON client');
        $this->addSql('DROP INDEX IDX_C74404558BAC62AF ON client');
        $this->addSql('ALTER TABLE client DROP country_id, DROP city_id');
    }
}
