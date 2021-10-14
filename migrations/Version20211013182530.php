<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211013182530 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649713C39D5');
        $this->addSql('DROP INDEX IDX_8D93D649713C39D5 ON user');
        $this->addSql('ALTER TABLE user DROP departemnt_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD departemnt_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649713C39D5 FOREIGN KEY (departemnt_id) REFERENCES departement (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649713C39D5 ON user (departemnt_id)');
    }
}
