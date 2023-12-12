<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231211204330 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cpv CHANGE etat_cpv etat_cpv TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE utilisateurs CHANGE etat_utilisateur etat_utilisateur TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cpv CHANGE etat_cpv etat_cpv VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE utilisateurs CHANGE etat_utilisateur etat_utilisateur VARCHAR(255) DEFAULT NULL');
    }
}
