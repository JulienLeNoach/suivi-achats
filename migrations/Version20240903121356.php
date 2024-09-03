<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240903121356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE achat ADD numero_marche VARCHAR(20) DEFAULT NULL, ADD numero_ej_marche VARCHAR(20) DEFAULT NULL, CHANGE numero_achat numero_achat VARCHAR(255) NOT NULL');
       
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE achat DROP numero_marche, DROP numero_ej_marche, CHANGE numero_achat numero_achat VARCHAR(255) DEFAULT NULL');
        
    }
}
