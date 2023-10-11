<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230509074626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendar DROP INDEX UNIQ_6EA9A1469D86650F, ADD INDEX IDX_6EA9A1469D86650F (user_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE achat CHANGE utilisateurs_id utilisateurs_id INT NOT NULL, CHANGE date_saisie date_saisie VARCHAR(255) NOT NULL, CHANGE numero_achat numero_achat INT DEFAULT NULL, CHANGE id_demande_achat id_demande_achat INT DEFAULT NULL, CHANGE date_sillage date_sillage VARCHAR(255) DEFAULT NULL, CHANGE date_commande_chorus date_commande_chorus VARCHAR(255) NOT NULL, CHANGE date_validation date_validation DATE DEFAULT NULL, CHANGE etat_achat etat_achat VARCHAR(255) DEFAULT \'0\'');
        $this->addSql('ALTER TABLE calendar DROP INDEX IDX_6EA9A1469D86650F, ADD UNIQUE INDEX UNIQ_6EA9A1469D86650F (user_id_id)');
        $this->addSql('ALTER TABLE calendar CHANGE title title VARCHAR(100) DEFAULT NULL, CHANGE description description TEXT DEFAULT NULL, CHANGE all_day all_day TINYINT(1) DEFAULT NULL, CHANGE background_color background_color VARCHAR(10) NOT NULL, CHANGE border_color border_color VARCHAR(7) DEFAULT NULL, CHANGE text_color text_color VARCHAR(7) DEFAULT NULL');
        $this->addSql('ALTER TABLE formations CHANGE code_formation code_formation INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fournisseurs CHANGE mobile mobile INT DEFAULT NULL');
    }
}
