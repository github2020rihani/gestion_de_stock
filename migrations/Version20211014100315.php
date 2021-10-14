<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211014100315 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fournisseur ADD category_id INT DEFAULT NULL, ADD country_id INT DEFAULT NULL, ADD city_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fournisseur ADD CONSTRAINT FK_369ECA3212469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE fournisseur ADD CONSTRAINT FK_369ECA32F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE fournisseur ADD CONSTRAINT FK_369ECA328BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_369ECA3212469DE2 ON fournisseur (category_id)');
        $this->addSql('CREATE INDEX IDX_369ECA32F92F3E70 ON fournisseur (country_id)');
        $this->addSql('CREATE INDEX IDX_369ECA328BAC62AF ON fournisseur (city_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fournisseur DROP FOREIGN KEY FK_369ECA3212469DE2');
        $this->addSql('ALTER TABLE fournisseur DROP FOREIGN KEY FK_369ECA32F92F3E70');
        $this->addSql('ALTER TABLE fournisseur DROP FOREIGN KEY FK_369ECA328BAC62AF');
        $this->addSql('DROP INDEX IDX_369ECA3212469DE2 ON fournisseur');
        $this->addSql('DROP INDEX IDX_369ECA32F92F3E70 ON fournisseur');
        $this->addSql('DROP INDEX IDX_369ECA328BAC62AF ON fournisseur');
        $this->addSql('ALTER TABLE fournisseur DROP category_id, DROP country_id, DROP city_id');
    }
}
