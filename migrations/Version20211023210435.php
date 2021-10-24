<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211023210435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE inventaire_article (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, inventaire_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_E52174A07294869C (article_id), INDEX IDX_E52174A0CE430A85 (inventaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE inventaire_article ADD CONSTRAINT FK_E52174A07294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE inventaire_article ADD CONSTRAINT FK_E52174A0CE430A85 FOREIGN KEY (inventaire_id) REFERENCES inventaire (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE inventaire_article');
    }
}
