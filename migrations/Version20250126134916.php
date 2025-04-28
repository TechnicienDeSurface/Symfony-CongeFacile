<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250126134916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE department (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, manager_id INT DEFAULT NULL, department_id INT NOT NULL, position_id INT NOT NULL, last_name VARCHAR(100) NOT NULL, first_name VARCHAR(100) NOT NULL, alert_new_request TINYINT(1) NOT NULL, alert_on_answer TINYINT(1) NOT NULL, alert_before_vacation TINYINT(1) NOT NULL, INDEX IDX_34DCD176783E3463 (manager_id), INDEX IDX_34DCD176AE80F5DF (department_id), INDEX IDX_34DCD176DD842E46 (position_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE position (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) UNIQUE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE request (id INT AUTO_INCREMENT NOT NULL, request_type_id INT NOT NULL, collaborator_id INT NOT NULL, department_id INT NOT NULL, created_at DATETIME NOT NULL, start_at DATETIME NOT NULL, end_at DATETIME NOT NULL, receipt_file VARCHAR(255) DEFAULT NULL, comment LONGTEXT DEFAULT NULL, answer_comment LONGTEXT DEFAULT NULL, answer TINYINT(1) DEFAULT NULL, answer_at DATETIME DEFAULT NULL, INDEX IDX_3B978F9FEF68FEC4 (request_type_id), INDEX IDX_3B978F9F30098C8C (collaborator_id), INDEX IDX_3B978F9FAE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE request_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, person_id INT NOT NULL, email VARCHAR(100) NOT NULL, password VARCHAR(100) NOT NULL, enabled TINYINT(1) NOT NULL, is_verified TINYINT(1) DEFAULT 0, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', role VARCHAR(50) NOT NULL, INDEX IDX_8D93D649217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176783E3463 FOREIGN KEY (manager_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176DD842E46 FOREIGN KEY (position_id) REFERENCES position (id)');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9FEF68FEC4 FOREIGN KEY (request_type_id) REFERENCES request_type (id)');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9F30098C8C FOREIGN KEY (collaborator_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9FAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('CREATE TABLE reset_password_request (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            token VARCHAR(255) NOT NULL,
            created_at DATETIME NOT NULL,
            expires_at DATETIME NOT NULL,
            is_verified TINYINT(1), 
            FOREIGN KEY (user_id) REFERENCES user(id)
        );
        ');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176783E3463');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176AE80F5DF');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176DD842E46');
        $this->addSql('ALTER TABLE request DROP FOREIGN KEY FK_3B978F9FEF68FEC4');
        $this->addSql('ALTER TABLE request DROP FOREIGN KEY FK_3B978F9F30098C8C');
        $this->addSql('ALTER TABLE request DROP FOREIGN KEY FK_3B978F9FAE80F5DF');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649217BBB47');
        $this->addSql('DROP TABLE department');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE position');
        $this->addSql('DROP TABLE request');
        $this->addSql('DROP TABLE request_type');
        $this->addSql('DROP TABLE user');
    }
}
