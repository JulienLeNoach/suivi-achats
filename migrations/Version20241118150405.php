<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241118150405 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gsbdd ADD code_service_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE gsbdd ADD CONSTRAINT FK_94A7479EC5F25400 FOREIGN KEY (code_service_id) REFERENCES services (id)');
        $this->addSql('CREATE INDEX IDX_94A7479EC5F25400 ON gsbdd (code_service_id)');
    }

    public function down(Schema $schema): void
    {
       
    }
}
