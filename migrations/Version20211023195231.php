<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211023195231 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP qte, DROP tva, DROP pu_ttc, DROP marge, DROP prix_vente');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD qte INT DEFAULT NULL, ADD tva DOUBLE PRECISION DEFAULT NULL, ADD pu_ttc DOUBLE PRECISION DEFAULT NULL, ADD marge DOUBLE PRECISION DEFAULT NULL, ADD prix_vente DOUBLE PRECISION DEFAULT NULL');
    }
}
