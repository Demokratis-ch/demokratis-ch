<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230118095425 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invite ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invite ADD CONSTRAINT FK_C7E210D7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7E210D7A76ED395 ON invite (user_id)');
        $this->addSql('ALTER TABLE organisation CHANGE is_personal_organisation is_personal_organisation TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invite DROP FOREIGN KEY FK_C7E210D7A76ED395');
        $this->addSql('DROP INDEX UNIQ_C7E210D7A76ED395 ON invite');
        $this->addSql('ALTER TABLE invite DROP user_id');
        $this->addSql('ALTER TABLE organisation CHANGE is_personal_organisation is_personal_organisation TINYINT(1) DEFAULT 0 NOT NULL');
    }
}
