<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230222143226 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE free_text (id INT AUTO_INCREMENT NOT NULL, paragraph_id INT NOT NULL, statement_id INT NOT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', text LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', position VARCHAR(255) NOT NULL, INDEX IDX_63EA7078B50597F (paragraph_id), INDEX IDX_63EA707849CB65B (statement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE free_text ADD CONSTRAINT FK_63EA7078B50597F FOREIGN KEY (paragraph_id) REFERENCES paragraph (id)');
        $this->addSql('ALTER TABLE free_text ADD CONSTRAINT FK_63EA707849CB65B FOREIGN KEY (statement_id) REFERENCES statement (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE free_text DROP FOREIGN KEY FK_63EA7078B50597F');
        $this->addSql('ALTER TABLE free_text DROP FOREIGN KEY FK_63EA707849CB65B');
        $this->addSql('DROP TABLE free_text');
    }
}
