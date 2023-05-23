<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230523174616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial migration file';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chosen_modification (id INT AUTO_INCREMENT NOT NULL, paragraph_id INT DEFAULT NULL, statement_id INT DEFAULT NULL, chosen_by_id INT NOT NULL, modification_statement_id INT DEFAULT NULL, chosen_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_2D35F0AE8B50597F (paragraph_id), INDEX IDX_2D35F0AE849CB65B (statement_id), INDEX IDX_2D35F0AEF5535B5B (chosen_by_id), UNIQUE INDEX UNIQ_2D35F0AEB10D6C71 (modification_statement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, thread_id INT NOT NULL, parent_id INT DEFAULT NULL, deleted_by_id INT DEFAULT NULL, text LONGTEXT NOT NULL, deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9474526CF675F31B (author_id), INDEX IDX_9474526CE2904019 (thread_id), INDEX IDX_9474526C727ACA70 (parent_id), INDEX IDX_9474526CC76F1F52 (deleted_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE consultation (id INT AUTO_INCREMENT NOT NULL, organisation_id INT DEFAULT NULL, single_statement_id INT DEFAULT NULL, title LONGTEXT DEFAULT NULL, description LONGTEXT DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, start_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', end_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', office VARCHAR(255) DEFAULT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', fedlex_id VARCHAR(255) DEFAULT NULL, institution VARCHAR(255) DEFAULT NULL, human_title VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_964685A69E6B1585 (organisation_id), UNIQUE INDEX UNIQ_964685A636277B14 (single_statement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact_request (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, answered TINYINT(1) DEFAULT NULL, answered_by VARCHAR(255) DEFAULT NULL, answered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discussion (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, consultation_id INT NOT NULL, thread_id INT NOT NULL, topic VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C0B9F90FB03A8386 (created_by_id), INDEX IDX_C0B9F90F62FF6CDF (consultation_id), UNIQUE INDEX UNIQ_C0B9F90FE2904019 (thread_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document (id INT AUTO_INCREMENT NOT NULL, consultation_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, filepath VARCHAR(255) DEFAULT NULL, fedlex_uri VARCHAR(255) DEFAULT NULL, filename VARCHAR(255) DEFAULT NULL, imported VARCHAR(255) DEFAULT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', local_filename VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_D8698A7662FF6CDF (consultation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE external_statement (id INT AUTO_INCREMENT NOT NULL, organisation_id INT DEFAULT NULL, consultation_id INT NOT NULL, created_by_id INT DEFAULT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, public TINYINT(1) NOT NULL, file VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_E480FE629E6B1585 (organisation_id), INDEX IDX_E480FE6262FF6CDF (consultation_id), INDEX IDX_E480FE62B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE free_text (id INT AUTO_INCREMENT NOT NULL, paragraph_id INT NOT NULL, statement_id INT NOT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', text LONGTEXT DEFAULT NULL, position VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_63EA7078B50597F (paragraph_id), INDEX IDX_63EA707849CB65B (statement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invite (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, user_id INT DEFAULT NULL, organisation_id INT DEFAULT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', email VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, invited_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', registered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_C7E210D7E7927C74 (email), UNIQUE INDEX UNIQ_C7E210D7217BBB47 (person_id), UNIQUE INDEX UNIQ_C7E210D7A76ED395 (user_id), INDEX IDX_C7E210D79E6B1585 (organisation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE legal_text (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, statement_id INT DEFAULT NULL, consultation_id INT DEFAULT NULL, imported_from_id INT DEFAULT NULL, title LONGTEXT DEFAULT NULL, text LONGTEXT DEFAULT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_16F04970B03A8386 (created_by_id), INDEX IDX_16F04970849CB65B (statement_id), INDEX IDX_16F0497062FF6CDF (consultation_id), UNIQUE INDEX UNIQ_16F04970AC328554 (imported_from_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, consultation_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, url VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_6A2CA10C62FF6CDF (consultation_id), INDEX IDX_6A2CA10CB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE membership (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, accepted_by_id INT DEFAULT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, street VARCHAR(255) DEFAULT NULL, zip VARCHAR(255) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, accepted TINYINT(1) DEFAULT NULL, accepted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_86FFD285B03A8386 (created_by_id), UNIQUE INDEX UNIQ_86FFD28520F699D9 (accepted_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE modification (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, paragraph_id INT NOT NULL, text LONGTEXT NOT NULL, justification VARCHAR(255) DEFAULT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_EF6425D2B03A8386 (created_by_id), INDEX IDX_EF6425D28B50597F (paragraph_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE modification_statement (id INT AUTO_INCREMENT NOT NULL, modification_id INT NOT NULL, statement_id INT NOT NULL, refused TINYINT(1) NOT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', decision_reason VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_BF8C104C4A605127 (modification_id), INDEX IDX_BF8C104C849CB65B (statement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE newsletter (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_7E8585C8E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organisation (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, public TINYINT(1) NOT NULL, is_personal_organisation TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organisation_tag (organisation_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_F511C3AA9E6B1585 (organisation_id), INDEX IDX_F511C3AABAD26311 (tag_id), PRIMARY KEY(organisation_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paragraph (id INT AUTO_INCREMENT NOT NULL, legal_text_id INT NOT NULL, text LONGTEXT NOT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', position INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7DD39862AEB6AB26 (legal_text_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_34DCD176A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE redirect (id INT AUTO_INCREMENT NOT NULL, consultation_id INT DEFAULT NULL, statement_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, organisation_id INT DEFAULT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', token VARCHAR(255) NOT NULL, password VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C30C9E2B62FF6CDF (consultation_id), INDEX IDX_C30C9E2B849CB65B (statement_id), INDEX IDX_C30C9E2BB03A8386 (created_by_id), INDEX IDX_C30C9E2B9E6B1585 (organisation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE statement (id INT AUTO_INCREMENT NOT NULL, consultation_id INT NOT NULL, organisation_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, justification VARCHAR(255) DEFAULT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', public TINYINT(1) NOT NULL, editable TINYINT(1) NOT NULL, intro LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C0DB517662FF6CDF (consultation_id), INDEX IDX_C0DB51769E6B1585 (organisation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE statement_user (statement_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_9FB3D665849CB65B (statement_id), INDEX IDX_9FB3D665A76ED395 (user_id), PRIMARY KEY(statement_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE approved_statements (statement_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_58BDE3A849CB65B (statement_id), INDEX IDX_58BDE3AA76ED395 (user_id), PRIMARY KEY(statement_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, approved TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_389B783B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_consultation (tag_id INT NOT NULL, consultation_id INT NOT NULL, INDEX IDX_3650AEA4BAD26311 (tag_id), INDEX IDX_3650AEA462FF6CDF (consultation_id), PRIMARY KEY(tag_id, consultation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE thread (id INT AUTO_INCREMENT NOT NULL, identifier VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unknown_institution (id INT AUTO_INCREMENT NOT NULL, consultation_id INT DEFAULT NULL, institution LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_CFF0A26362FF6CDF (consultation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, active_organisation_id INT DEFAULT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D6499E53131D (active_organisation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_organisation (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, organisation_id INT NOT NULL, is_admin TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_662D4EB6A76ED395 (user_id), INDEX IDX_662D4EB69E6B1585 (organisation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vote (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, comment_id INT DEFAULT NULL, vote VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_5A108564F675F31B (author_id), INDEX IDX_5A108564F8697D13 (comment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE whatwedo_search_index (id BIGINT AUTO_INCREMENT NOT NULL, foreign_id INT NOT NULL, model VARCHAR(150) NOT NULL, grp VARCHAR(90) NOT NULL, content LONGTEXT NOT NULL, FULLTEXT INDEX IDX_38033FA6FEC530A9 (content), INDEX IDX_38033FA6D79572D9 (model), UNIQUE INDEX search_index (foreign_id, model, grp), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chosen_modification ADD CONSTRAINT FK_2D35F0AE8B50597F FOREIGN KEY (paragraph_id) REFERENCES paragraph (id)');
        $this->addSql('ALTER TABLE chosen_modification ADD CONSTRAINT FK_2D35F0AE849CB65B FOREIGN KEY (statement_id) REFERENCES statement (id)');
        $this->addSql('ALTER TABLE chosen_modification ADD CONSTRAINT FK_2D35F0AEF5535B5B FOREIGN KEY (chosen_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE chosen_modification ADD CONSTRAINT FK_2D35F0AEB10D6C71 FOREIGN KEY (modification_statement_id) REFERENCES modification_statement (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CE2904019 FOREIGN KEY (thread_id) REFERENCES thread (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C727ACA70 FOREIGN KEY (parent_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CC76F1F52 FOREIGN KEY (deleted_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A69E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id)');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A636277B14 FOREIGN KEY (single_statement_id) REFERENCES statement (id)');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90FB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90F62FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90FE2904019 FOREIGN KEY (thread_id) REFERENCES thread (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A7662FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
        $this->addSql('ALTER TABLE external_statement ADD CONSTRAINT FK_E480FE629E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id)');
        $this->addSql('ALTER TABLE external_statement ADD CONSTRAINT FK_E480FE6262FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
        $this->addSql('ALTER TABLE external_statement ADD CONSTRAINT FK_E480FE62B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE free_text ADD CONSTRAINT FK_63EA7078B50597F FOREIGN KEY (paragraph_id) REFERENCES paragraph (id)');
        $this->addSql('ALTER TABLE free_text ADD CONSTRAINT FK_63EA707849CB65B FOREIGN KEY (statement_id) REFERENCES statement (id)');
        $this->addSql('ALTER TABLE invite ADD CONSTRAINT FK_C7E210D7217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE invite ADD CONSTRAINT FK_C7E210D7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE invite ADD CONSTRAINT FK_C7E210D79E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE legal_text ADD CONSTRAINT FK_16F04970B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE legal_text ADD CONSTRAINT FK_16F04970849CB65B FOREIGN KEY (statement_id) REFERENCES statement (id)');
        $this->addSql('ALTER TABLE legal_text ADD CONSTRAINT FK_16F0497062FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
        $this->addSql('ALTER TABLE legal_text ADD CONSTRAINT FK_16F04970AC328554 FOREIGN KEY (imported_from_id) REFERENCES document (id)');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C62FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE membership ADD CONSTRAINT FK_86FFD285B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE membership ADD CONSTRAINT FK_86FFD28520F699D9 FOREIGN KEY (accepted_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE modification ADD CONSTRAINT FK_EF6425D2B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE modification ADD CONSTRAINT FK_EF6425D28B50597F FOREIGN KEY (paragraph_id) REFERENCES paragraph (id)');
        $this->addSql('ALTER TABLE modification_statement ADD CONSTRAINT FK_BF8C104C4A605127 FOREIGN KEY (modification_id) REFERENCES modification (id)');
        $this->addSql('ALTER TABLE modification_statement ADD CONSTRAINT FK_BF8C104C849CB65B FOREIGN KEY (statement_id) REFERENCES statement (id)');
        $this->addSql('ALTER TABLE organisation_tag ADD CONSTRAINT FK_F511C3AA9E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE organisation_tag ADD CONSTRAINT FK_F511C3AABAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE paragraph ADD CONSTRAINT FK_7DD39862AEB6AB26 FOREIGN KEY (legal_text_id) REFERENCES legal_text (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE redirect ADD CONSTRAINT FK_C30C9E2B62FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
        $this->addSql('ALTER TABLE redirect ADD CONSTRAINT FK_C30C9E2B849CB65B FOREIGN KEY (statement_id) REFERENCES statement (id)');
        $this->addSql('ALTER TABLE redirect ADD CONSTRAINT FK_C30C9E2BB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE redirect ADD CONSTRAINT FK_C30C9E2B9E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE statement ADD CONSTRAINT FK_C0DB517662FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
        $this->addSql('ALTER TABLE statement ADD CONSTRAINT FK_C0DB51769E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id)');
        $this->addSql('ALTER TABLE statement_user ADD CONSTRAINT FK_9FB3D665849CB65B FOREIGN KEY (statement_id) REFERENCES statement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE statement_user ADD CONSTRAINT FK_9FB3D665A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE approved_statements ADD CONSTRAINT FK_58BDE3A849CB65B FOREIGN KEY (statement_id) REFERENCES statement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE approved_statements ADD CONSTRAINT FK_58BDE3AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B783B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tag_consultation ADD CONSTRAINT FK_3650AEA4BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_consultation ADD CONSTRAINT FK_3650AEA462FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE unknown_institution ADD CONSTRAINT FK_CFF0A26362FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6499E53131D FOREIGN KEY (active_organisation_id) REFERENCES organisation (id)');
        $this->addSql('ALTER TABLE user_organisation ADD CONSTRAINT FK_662D4EB6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_organisation ADD CONSTRAINT FK_662D4EB69E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chosen_modification DROP FOREIGN KEY FK_2D35F0AE8B50597F');
        $this->addSql('ALTER TABLE chosen_modification DROP FOREIGN KEY FK_2D35F0AE849CB65B');
        $this->addSql('ALTER TABLE chosen_modification DROP FOREIGN KEY FK_2D35F0AEF5535B5B');
        $this->addSql('ALTER TABLE chosen_modification DROP FOREIGN KEY FK_2D35F0AEB10D6C71');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CE2904019');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C727ACA70');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CC76F1F52');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A69E6B1585');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A636277B14');
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90FB03A8386');
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90F62FF6CDF');
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90FE2904019');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A7662FF6CDF');
        $this->addSql('ALTER TABLE external_statement DROP FOREIGN KEY FK_E480FE629E6B1585');
        $this->addSql('ALTER TABLE external_statement DROP FOREIGN KEY FK_E480FE6262FF6CDF');
        $this->addSql('ALTER TABLE external_statement DROP FOREIGN KEY FK_E480FE62B03A8386');
        $this->addSql('ALTER TABLE free_text DROP FOREIGN KEY FK_63EA7078B50597F');
        $this->addSql('ALTER TABLE free_text DROP FOREIGN KEY FK_63EA707849CB65B');
        $this->addSql('ALTER TABLE invite DROP FOREIGN KEY FK_C7E210D7217BBB47');
        $this->addSql('ALTER TABLE invite DROP FOREIGN KEY FK_C7E210D7A76ED395');
        $this->addSql('ALTER TABLE invite DROP FOREIGN KEY FK_C7E210D79E6B1585');
        $this->addSql('ALTER TABLE legal_text DROP FOREIGN KEY FK_16F04970B03A8386');
        $this->addSql('ALTER TABLE legal_text DROP FOREIGN KEY FK_16F04970849CB65B');
        $this->addSql('ALTER TABLE legal_text DROP FOREIGN KEY FK_16F0497062FF6CDF');
        $this->addSql('ALTER TABLE legal_text DROP FOREIGN KEY FK_16F04970AC328554');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C62FF6CDF');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CB03A8386');
        $this->addSql('ALTER TABLE membership DROP FOREIGN KEY FK_86FFD285B03A8386');
        $this->addSql('ALTER TABLE membership DROP FOREIGN KEY FK_86FFD28520F699D9');
        $this->addSql('ALTER TABLE modification DROP FOREIGN KEY FK_EF6425D2B03A8386');
        $this->addSql('ALTER TABLE modification DROP FOREIGN KEY FK_EF6425D28B50597F');
        $this->addSql('ALTER TABLE modification_statement DROP FOREIGN KEY FK_BF8C104C4A605127');
        $this->addSql('ALTER TABLE modification_statement DROP FOREIGN KEY FK_BF8C104C849CB65B');
        $this->addSql('ALTER TABLE organisation_tag DROP FOREIGN KEY FK_F511C3AA9E6B1585');
        $this->addSql('ALTER TABLE organisation_tag DROP FOREIGN KEY FK_F511C3AABAD26311');
        $this->addSql('ALTER TABLE paragraph DROP FOREIGN KEY FK_7DD39862AEB6AB26');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176A76ED395');
        $this->addSql('ALTER TABLE redirect DROP FOREIGN KEY FK_C30C9E2B62FF6CDF');
        $this->addSql('ALTER TABLE redirect DROP FOREIGN KEY FK_C30C9E2B849CB65B');
        $this->addSql('ALTER TABLE redirect DROP FOREIGN KEY FK_C30C9E2BB03A8386');
        $this->addSql('ALTER TABLE redirect DROP FOREIGN KEY FK_C30C9E2B9E6B1585');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE statement DROP FOREIGN KEY FK_C0DB517662FF6CDF');
        $this->addSql('ALTER TABLE statement DROP FOREIGN KEY FK_C0DB51769E6B1585');
        $this->addSql('ALTER TABLE statement_user DROP FOREIGN KEY FK_9FB3D665849CB65B');
        $this->addSql('ALTER TABLE statement_user DROP FOREIGN KEY FK_9FB3D665A76ED395');
        $this->addSql('ALTER TABLE approved_statements DROP FOREIGN KEY FK_58BDE3A849CB65B');
        $this->addSql('ALTER TABLE approved_statements DROP FOREIGN KEY FK_58BDE3AA76ED395');
        $this->addSql('ALTER TABLE tag DROP FOREIGN KEY FK_389B783B03A8386');
        $this->addSql('ALTER TABLE tag_consultation DROP FOREIGN KEY FK_3650AEA4BAD26311');
        $this->addSql('ALTER TABLE tag_consultation DROP FOREIGN KEY FK_3650AEA462FF6CDF');
        $this->addSql('ALTER TABLE unknown_institution DROP FOREIGN KEY FK_CFF0A26362FF6CDF');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499E53131D');
        $this->addSql('ALTER TABLE user_organisation DROP FOREIGN KEY FK_662D4EB6A76ED395');
        $this->addSql('ALTER TABLE user_organisation DROP FOREIGN KEY FK_662D4EB69E6B1585');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564F675F31B');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564F8697D13');
        $this->addSql('DROP TABLE chosen_modification');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE consultation');
        $this->addSql('DROP TABLE contact_request');
        $this->addSql('DROP TABLE discussion');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE external_statement');
        $this->addSql('DROP TABLE free_text');
        $this->addSql('DROP TABLE invite');
        $this->addSql('DROP TABLE legal_text');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE membership');
        $this->addSql('DROP TABLE modification');
        $this->addSql('DROP TABLE modification_statement');
        $this->addSql('DROP TABLE newsletter');
        $this->addSql('DROP TABLE organisation');
        $this->addSql('DROP TABLE organisation_tag');
        $this->addSql('DROP TABLE paragraph');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE redirect');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE statement');
        $this->addSql('DROP TABLE statement_user');
        $this->addSql('DROP TABLE approved_statements');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_consultation');
        $this->addSql('DROP TABLE thread');
        $this->addSql('DROP TABLE unknown_institution');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_organisation');
        $this->addSql('DROP TABLE vote');
        $this->addSql('DROP TABLE whatwedo_search_index');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
