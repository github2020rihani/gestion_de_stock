<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211101172207 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE devis (id INT AUTO_INCREMENT NOT NULL, creadet_by_id INT DEFAULT NULL, client_id INT DEFAULT NULL, numero VARCHAR(255) DEFAULT NULL, creadet_at DATETIME NOT NULL, total_ttc DOUBLE PRECISION DEFAULT NULL, status TINYINT(1) NOT NULL, finished TINYINT(1) NOT NULL, INDEX IDX_8B27C52B7B245D39 (creadet_by_id), INDEX IDX_8B27C52B19EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE devis_article (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, devi_id INT DEFAULT NULL, qte INT DEFAULT NULL, total DOUBLE PRECISION DEFAULT NULL, pventettc DOUBLE PRECISION DEFAULT NULL, remise DOUBLE PRECISION DEFAULT NULL, INDEX IDX_90D4953A7294869C (article_id), INDEX IDX_90D4953A131098A5 (devi_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52B7B245D39 FOREIGN KEY (creadet_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52B19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE devis_article ADD CONSTRAINT FK_90D4953A7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE devis_article ADD CONSTRAINT FK_90D4953A131098A5 FOREIGN KEY (devi_id) REFERENCES devis (id)');
        $this->addSql('ALTER TABLE article ADD remise DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis_article DROP FOREIGN KEY FK_90D4953A131098A5');
        $this->addSql('DROP TABLE devis');
        $this->addSql('DROP TABLE devis_article');
        $this->addSql('ALTER TABLE article DROP remise');
    }
}
