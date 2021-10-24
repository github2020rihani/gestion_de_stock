<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211023222157 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventaire_article ADD pr_achat_ht DOUBLE PRECISION DEFAULT NULL, ADD pr_achat_ttc DOUBLE PRECISION DEFAULT NULL, ADD gte DOUBLE PRECISION DEFAULT NULL, ADD total_ttc DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventaire_article DROP pr_achat_ht, DROP pr_achat_ttc, DROP gte, DROP total_ttc');
    }
}
