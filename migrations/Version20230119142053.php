<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230119142053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE organisation_user DROP FOREIGN KEY FK_CFD7D6519E6B1585');
        $this->addSql('ALTER TABLE organisation_user DROP FOREIGN KEY FK_CFD7D651A76ED395');
        $this->addSql('DROP TABLE organisation_user');
        $this->addSql('ALTER TABLE user_organisation DROP FOREIGN KEY FK_662D4EB69E6B1585');
        $this->addSql('ALTER TABLE user_organisation DROP FOREIGN KEY FK_662D4EB6A76ED395');
        $this->addSql('ALTER TABLE user_organisation ADD id INT AUTO_INCREMENT NOT NULL, ADD is_admin TINYINT(1) NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE user_organisation ADD CONSTRAINT FK_662D4EB69E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id)');
        $this->addSql('ALTER TABLE user_organisation ADD CONSTRAINT FK_662D4EB6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE organisation_user (organisation_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_CFD7D6519E6B1585 (organisation_id), INDEX IDX_CFD7D651A76ED395 (user_id), PRIMARY KEY(organisation_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE organisation_user ADD CONSTRAINT FK_CFD7D6519E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE organisation_user ADD CONSTRAINT FK_CFD7D651A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_organisation MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE user_organisation DROP FOREIGN KEY FK_662D4EB6A76ED395');
        $this->addSql('ALTER TABLE user_organisation DROP FOREIGN KEY FK_662D4EB69E6B1585');
        $this->addSql('DROP INDEX `PRIMARY` ON user_organisation');
        $this->addSql('ALTER TABLE user_organisation DROP id, DROP is_admin');
        $this->addSql('ALTER TABLE user_organisation ADD CONSTRAINT FK_662D4EB6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_organisation ADD CONSTRAINT FK_662D4EB69E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_organisation ADD PRIMARY KEY (user_id, organisation_id)');
    }
}
