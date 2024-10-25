<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241022140653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE devis (id INT AUTO_INCREMENT NOT NULL, achat_id INT DEFAULT NULL, nom_candidat VARCHAR(255) NOT NULL, montant_ht INT NOT NULL, obs VARCHAR(255) DEFAULT NULL, INDEX IDX_8B27C52BFE95D117 (achat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE justif_achat (id INT AUTO_INCREMENT NOT NULL, type_justif VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52BFE95D117 FOREIGN KEY (achat_id) REFERENCES achat (id)');
   $this->addSql('ALTER TABLE achat ADD justif_achat_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE achat ADD CONSTRAINT FK_26A984567D6010A8 FOREIGN KEY (justif_achat_id) REFERENCES justif_achat (id)');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
