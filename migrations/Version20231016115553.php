<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231016115553 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE achat CHANGE numero_achat numero_achat VARCHAR(6000) DEFAULT NULL');
        $this->addSql('ALTER TABLE achat CHANGE id_demande_achat id_demande_achat DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE achat CHANGE date_validation date_validation DATETIME DEFAULT NULL');

        $this->addSql('ALTER TABLE calendar CHANGE title title VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE calendar CHANGE start start DATETIME NOT NULL');
        $this->addSql('ALTER TABLE calendar CHANGE description description VARCHAR(1000) DEFAULT NULL'); // Augmentez la taille à VARCHAR(1000)

        $this->addSql('ALTER TABLE calendar CHANGE all_day all_day TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE calendar CHANGE background_color background_color VARCHAR(7) NOT NULL');
        $this->addSql('ALTER TABLE calendar CHANGE border_color border_color VARCHAR(7) DEFAULT NULL');
        $this->addSql('ALTER TABLE calendar CHANGE text_color text_color VARCHAR(7) DEFAULT NULL');

        $this->addSql('ALTER TABLE formations CHANGE code_formation code_formation VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fournisseurs CHANGE mobile mobile VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE achat CHANGE numero_achat numero_achat VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE achat CHANGE id_demande_achat id_demande_achat DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE achat CHANGE date_validation date_validation DATETIME DEFAULT NULL');

        $this->addSql('ALTER TABLE calendar CHANGE title title VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE calendar CHANGE start start DATETIME NOT NULL');
        $this->addSql('ALTER TABLE calendar CHANGE description description VARCHAR(1000) DEFAULT NULL'); // Augmentez la taille à VARCHAR(1000)

        $this->addSql('ALTER TABLE calendar CHANGE all_day all_day TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE calendar CHANGE background_color background_color VARCHAR(7) NOT NULL');
        $this->addSql('ALTER TABLE calendar CHANGE border_color border_color VARCHAR(7) DEFAULT NULL');
        $this->addSql('ALTER TABLE calendar CHANGE text_color text_color VARCHAR(7) DEFAULT NULL');

        $this->addSql('ALTER TABLE formations CHANGE code_formation code_formation VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fournisseurs CHANGE mobile mobile VARCHAR(255) DEFAULT NULL');
    }
}
