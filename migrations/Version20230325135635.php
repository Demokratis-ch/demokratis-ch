<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230325135635 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE redirect (id INT AUTO_INCREMENT NOT NULL, consultation_id INT DEFAULT NULL, statement_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, organisation_id INT DEFAULT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', token VARCHAR(255) NOT NULL, password VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C30C9E2B62FF6CDF (consultation_id), INDEX IDX_C30C9E2B849CB65B (statement_id), INDEX IDX_C30C9E2BB03A8386 (created_by_id), INDEX IDX_C30C9E2B9E6B1585 (organisation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE redirect ADD CONSTRAINT FK_C30C9E2B62FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
        $this->addSql('ALTER TABLE redirect ADD CONSTRAINT FK_C30C9E2B849CB65B FOREIGN KEY (statement_id) REFERENCES statement (id)');
        $this->addSql('ALTER TABLE redirect ADD CONSTRAINT FK_C30C9E2BB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE redirect ADD CONSTRAINT FK_C30C9E2B9E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE redirect DROP FOREIGN KEY FK_C30C9E2B62FF6CDF');
        $this->addSql('ALTER TABLE redirect DROP FOREIGN KEY FK_C30C9E2B849CB65B');
        $this->addSql('ALTER TABLE redirect DROP FOREIGN KEY FK_C30C9E2BB03A8386');
        $this->addSql('ALTER TABLE redirect DROP FOREIGN KEY FK_C30C9E2B9E6B1585');
        $this->addSql('DROP TABLE redirect');
    }
}
