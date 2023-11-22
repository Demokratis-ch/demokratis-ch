<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231018195325 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE paragraph_statement (id INT AUTO_INCREMENT NOT NULL, paragraph_id INT NOT NULL, statement_id INT NOT NULL, thread_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_2C3504148B50597F (paragraph_id), INDEX IDX_2C350414849CB65B (statement_id), UNIQUE INDEX UNIQ_2C350414E2904019 (thread_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE paragraph_statement ADD CONSTRAINT FK_2C3504148B50597F FOREIGN KEY (paragraph_id) REFERENCES paragraph (id)');
        $this->addSql('ALTER TABLE paragraph_statement ADD CONSTRAINT FK_2C350414849CB65B FOREIGN KEY (statement_id) REFERENCES statement (id)');
        $this->addSql('ALTER TABLE paragraph_statement ADD CONSTRAINT FK_2C350414E2904019 FOREIGN KEY (thread_id) REFERENCES thread (id)');
        $this->addSql('ALTER TABLE discussion CHANGE thread_id thread_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE modification_statement ADD thread_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE modification_statement ADD CONSTRAINT FK_BF8C104CE2904019 FOREIGN KEY (thread_id) REFERENCES thread (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BF8C104CE2904019 ON modification_statement (thread_id)');
        $this->addSql('ALTER TABLE thread DROP COLUMN identifier');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paragraph_statement DROP FOREIGN KEY FK_2C3504148B50597F');
        $this->addSql('ALTER TABLE paragraph_statement DROP FOREIGN KEY FK_2C350414849CB65B');
        $this->addSql('ALTER TABLE paragraph_statement DROP FOREIGN KEY FK_2C350414E2904019');
        $this->addSql('DROP TABLE paragraph_statement');
        $this->addSql('ALTER TABLE modification_statement DROP FOREIGN KEY FK_BF8C104CE2904019');
        $this->addSql('DROP INDEX UNIQ_BF8C104CE2904019 ON modification_statement');
        $this->addSql('ALTER TABLE modification_statement DROP thread_id');
        $this->addSql('ALTER TABLE discussion CHANGE thread_id thread_id INT NOT NULL');
        $this->addSql('ALTER TABLE thread ADD COLUMN identifier varchar(255)');
    }
}
