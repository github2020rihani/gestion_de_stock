<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211017153231 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, added_by_id INT DEFAULT NULL, ref VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, qte INT NOT NULL, tva DOUBLE PRECISION NOT NULL, pu_ttc DOUBLE PRECISION NOT NULL, marge DOUBLE PRECISION NOT NULL, prix_vente DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_23A0E6655B127A4 (added_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6655B127A4 FOREIGN KEY (added_by_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE departement_produit');
        $this->addSql('DROP TABLE entree');
        $this->addSql('ALTER TABLE fournisseur CHANGE email email VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE produit DROP remise, CHANGE tva tva VARCHAR(50) NOT NULL, CHANGE qte_sec qte_sec VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE departement_produit (id INT AUTO_INCREMENT NOT NULL, produit_id INT DEFAULT NULL, departement_id INT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_2B5538F8F347EFB (produit_id), INDEX IDX_2B5538F8CCF9E01E (departement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE entree (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, produit_id INT NOT NULL, created_at DATETIME NOT NULL, qte_secour INT NOT NULL, qte_total INT NOT NULL, qte INT NOT NULL, INDEX IDX_598377A6F347EFB (produit_id), INDEX IDX_598377A6B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE departement_produit ADD CONSTRAINT FK_2B5538F8CCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id)');
        $this->addSql('ALTER TABLE departement_produit ADD CONSTRAINT FK_2B5538F8F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE entree ADD CONSTRAINT FK_598377A6B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE entree ADD CONSTRAINT FK_598377A6F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('DROP TABLE article');
        $this->addSql('ALTER TABLE fournisseur CHANGE email email VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE produit ADD remise DOUBLE PRECISION DEFAULT NULL, CHANGE tva tva INT NOT NULL, CHANGE qte_sec qte_sec INT NOT NULL');
    }
}
