<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230315084130 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consultation ADD organisation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A69E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id)');
        $this->addSql('CREATE INDEX IDX_964685A69E6B1585 ON consultation (organisation_id)');
        $this->addSql('ALTER TABLE media CHANGE created_at created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A69E6B1585');
        $this->addSql('DROP INDEX IDX_964685A69E6B1585 ON consultation');
        $this->addSql('ALTER TABLE consultation DROP organisation_id');
        $this->addSql('ALTER TABLE media CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
