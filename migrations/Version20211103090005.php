<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211103090005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bond_livraison (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, customer_id INT DEFAULT NULL, numero VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, type_payement INT DEFAULT NULL, status TINYINT(1) DEFAULT NULL, exist_devi TINYINT(1) NOT NULL, total_ttc DOUBLE PRECISION DEFAULT NULL, total_ht DOUBLE PRECISION DEFAULT NULL, total_htnet DOUBLE PRECISION DEFAULT NULL, total_remise DOUBLE PRECISION DEFAULT NULL, total_tva VARCHAR(255) DEFAULT NULL, INDEX IDX_BCFF31F0B03A8386 (created_by_id), INDEX IDX_BCFF31F09395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bonlivraison_article (id INT AUTO_INCREMENT NOT NULL, bon_livraison_id INT DEFAULT NULL, article_id INT DEFAULT NULL, qte INT DEFAULT NULL, puht DOUBLE PRECISION DEFAULT NULL, puhtnet DOUBLE PRECISION DEFAULT NULL, remise DOUBLE PRECISION DEFAULT NULL, taxe DOUBLE PRECISION DEFAULT NULL, totalht DOUBLE PRECISION DEFAULT NULL, puttc DOUBLE PRECISION DEFAULT NULL, totalttc DOUBLE PRECISION DEFAULT NULL, INDEX IDX_B7C95A8ED8D16068 (bon_livraison_id), INDEX IDX_B7C95A8E7294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bond_livraison ADD CONSTRAINT FK_BCFF31F0B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE bond_livraison ADD CONSTRAINT FK_BCFF31F09395C3F3 FOREIGN KEY (customer_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE bonlivraison_article ADD CONSTRAINT FK_B7C95A8ED8D16068 FOREIGN KEY (bon_livraison_id) REFERENCES bond_livraison (id)');
        $this->addSql('ALTER TABLE bonlivraison_article ADD CONSTRAINT FK_B7C95A8E7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bonlivraison_article DROP FOREIGN KEY FK_B7C95A8ED8D16068');
        $this->addSql('DROP TABLE bond_livraison');
        $this->addSql('DROP TABLE bonlivraison_article');
    }
}
