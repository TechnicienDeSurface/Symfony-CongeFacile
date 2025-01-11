<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250111175757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE department (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, manager_id_id INT NOT NULL, department_id_id INT NOT NULL, position_id_id INT NOT NULL, last_name VARCHAR(100) NOT NULL, first_name VARCHAR(100) NOT NULL, alert_new_request TINYINT(1) NOT NULL, alert_on_answer TINYINT(1) NOT NULL, alert_before_vacation TINYINT(1) NOT NULL, INDEX IDX_34DCD176569B5E6D (manager_id_id), INDEX IDX_34DCD17664E7214B (department_id_id), INDEX IDX_34DCD176F3847A8A (position_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE position (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE request (id INT AUTO_INCREMENT NOT NULL, request_type_id_id INT NOT NULL, collaborator_id_id INT NOT NULL, department_id_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', start_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', end_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', receipt_file VARCHAR(255) DEFAULT NULL, comment LONGTEXT DEFAULT NULL, answer_comment LONGTEXT DEFAULT NULL, answer TINYINT(1) DEFAULT NULL, answer_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_3B978F9F39B1DE1A (request_type_id_id), INDEX IDX_3B978F9F60844807 (collaborator_id_id), INDEX IDX_3B978F9F64E7214B (department_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE request_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, person_id_id INT NOT NULL, email VARCHAR(100) NOT NULL, password VARCHAR(100) NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', role VARCHAR(50) NOT NULL, INDEX IDX_8D93D649D3728193 (person_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176569B5E6D FOREIGN KEY (manager_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD17664E7214B FOREIGN KEY (department_id_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176F3847A8A FOREIGN KEY (position_id_id) REFERENCES position (id)');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9F39B1DE1A FOREIGN KEY (request_type_id_id) REFERENCES request_type (id)');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9F60844807 FOREIGN KEY (collaborator_id_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9F64E7214B FOREIGN KEY (department_id_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D3728193 FOREIGN KEY (person_id_id) REFERENCES person (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176569B5E6D');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD17664E7214B');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176F3847A8A');
        $this->addSql('ALTER TABLE request DROP FOREIGN KEY FK_3B978F9F39B1DE1A');
        $this->addSql('ALTER TABLE request DROP FOREIGN KEY FK_3B978F9F60844807');
        $this->addSql('ALTER TABLE request DROP FOREIGN KEY FK_3B978F9F64E7214B');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D3728193');
        $this->addSql('DROP TABLE department');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE position');
        $this->addSql('DROP TABLE request');
        $this->addSql('DROP TABLE request_type');
        $this->addSql('DROP TABLE user');
    }
}
