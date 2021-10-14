<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211014065004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C12B36786B ON category (title)');
        $this->addSql('ALTER TABLE client ADD location_id INT DEFAULT NULL, ADD country VARCHAR(255) NOT NULL, ADD city VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C744045564D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('CREATE INDEX IDX_C744045564D218E ON client (location_id)');
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB19EB6921');
        $this->addSql('DROP INDEX UNIQ_5E9E89CB19EB6921 ON location');
        $this->addSql('ALTER TABLE location DROP client_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_64C19C12B36786B ON category');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C744045564D218E');
        $this->addSql('DROP INDEX IDX_C744045564D218E ON client');
        $this->addSql('ALTER TABLE client DROP location_id, DROP country, DROP city');
        $this->addSql('ALTER TABLE location ADD client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5E9E89CB19EB6921 ON location (client_id)');
    }
}
