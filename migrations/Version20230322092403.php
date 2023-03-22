<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230322092403 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consultation ADD single_statement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A636277B14 FOREIGN KEY (single_statement_id) REFERENCES statement (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_964685A637ED89E4 ON consultation (fedlex_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_964685A636277B14 ON consultation (single_statement_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A636277B14');
        $this->addSql('DROP INDEX UNIQ_964685A637ED89E4 ON consultation');
        $this->addSql('DROP INDEX UNIQ_964685A636277B14 ON consultation');
        $this->addSql('ALTER TABLE consultation DROP single_statement_id');
    }
}
