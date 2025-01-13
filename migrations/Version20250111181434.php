<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250111181434 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176569B5E6D');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD17664E7214B');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176F3847A8A');
        $this->addSql('DROP INDEX IDX_34DCD176569B5E6D ON person');
        $this->addSql('DROP INDEX IDX_34DCD17664E7214B ON person');
        $this->addSql('DROP INDEX IDX_34DCD176F3847A8A ON person');
        $this->addSql('ALTER TABLE person ADD manager_id INT NOT NULL, ADD department_id INT NOT NULL, ADD position_id INT NOT NULL, DROP manager_id_id, DROP department_id_id, DROP position_id_id');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176783E3463 FOREIGN KEY (manager_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176DD842E46 FOREIGN KEY (position_id) REFERENCES position (id)');
        $this->addSql('CREATE INDEX IDX_34DCD176783E3463 ON person (manager_id)');
        $this->addSql('CREATE INDEX IDX_34DCD176AE80F5DF ON person (department_id)');
        $this->addSql('CREATE INDEX IDX_34DCD176DD842E46 ON person (position_id)');
        $this->addSql('ALTER TABLE request DROP FOREIGN KEY FK_3B978F9F39B1DE1A');
        $this->addSql('ALTER TABLE request DROP FOREIGN KEY FK_3B978F9F60844807');
        $this->addSql('ALTER TABLE request DROP FOREIGN KEY FK_3B978F9F64E7214B');
        $this->addSql('DROP INDEX IDX_3B978F9F39B1DE1A ON request');
        $this->addSql('DROP INDEX IDX_3B978F9F60844807 ON request');
        $this->addSql('DROP INDEX IDX_3B978F9F64E7214B ON request');
        $this->addSql('ALTER TABLE request ADD request_type_id INT NOT NULL, ADD collaborator_id INT NOT NULL, ADD department_id INT NOT NULL, DROP request_type_id_id, DROP collaborator_id_id, DROP department_id_id');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9FEF68FEC4 FOREIGN KEY (request_type_id) REFERENCES request_type (id)');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9F30098C8C FOREIGN KEY (collaborator_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9FAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('CREATE INDEX IDX_3B978F9FEF68FEC4 ON request (request_type_id)');
        $this->addSql('CREATE INDEX IDX_3B978F9F30098C8C ON request (collaborator_id)');
        $this->addSql('CREATE INDEX IDX_3B978F9FAE80F5DF ON request (department_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D3728193');
        $this->addSql('DROP INDEX IDX_8D93D649D3728193 ON user');
        $this->addSql('ALTER TABLE user CHANGE person_id_id person_id INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649217BBB47 ON user (person_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE request DROP FOREIGN KEY FK_3B978F9FEF68FEC4');
        $this->addSql('ALTER TABLE request DROP FOREIGN KEY FK_3B978F9F30098C8C');
        $this->addSql('ALTER TABLE request DROP FOREIGN KEY FK_3B978F9FAE80F5DF');
        $this->addSql('DROP INDEX IDX_3B978F9FEF68FEC4 ON request');
        $this->addSql('DROP INDEX IDX_3B978F9F30098C8C ON request');
        $this->addSql('DROP INDEX IDX_3B978F9FAE80F5DF ON request');
        $this->addSql('ALTER TABLE request ADD request_type_id_id INT NOT NULL, ADD collaborator_id_id INT NOT NULL, ADD department_id_id INT NOT NULL, DROP request_type_id, DROP collaborator_id, DROP department_id');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9F39B1DE1A FOREIGN KEY (request_type_id_id) REFERENCES request_type (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9F60844807 FOREIGN KEY (collaborator_id_id) REFERENCES person (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9F64E7214B FOREIGN KEY (department_id_id) REFERENCES department (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_3B978F9F39B1DE1A ON request (request_type_id_id)');
        $this->addSql('CREATE INDEX IDX_3B978F9F60844807 ON request (collaborator_id_id)');
        $this->addSql('CREATE INDEX IDX_3B978F9F64E7214B ON request (department_id_id)');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176783E3463');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176AE80F5DF');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176DD842E46');
        $this->addSql('DROP INDEX IDX_34DCD176783E3463 ON person');
        $this->addSql('DROP INDEX IDX_34DCD176AE80F5DF ON person');
        $this->addSql('DROP INDEX IDX_34DCD176DD842E46 ON person');
        $this->addSql('ALTER TABLE person ADD manager_id_id INT NOT NULL, ADD department_id_id INT NOT NULL, ADD position_id_id INT NOT NULL, DROP manager_id, DROP department_id, DROP position_id');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176569B5E6D FOREIGN KEY (manager_id_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD17664E7214B FOREIGN KEY (department_id_id) REFERENCES department (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176F3847A8A FOREIGN KEY (position_id_id) REFERENCES position (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_34DCD176569B5E6D ON person (manager_id_id)');
        $this->addSql('CREATE INDEX IDX_34DCD17664E7214B ON person (department_id_id)');
        $this->addSql('CREATE INDEX IDX_34DCD176F3847A8A ON person (position_id_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649217BBB47');
        $this->addSql('DROP INDEX IDX_8D93D649217BBB47 ON user');
        $this->addSql('ALTER TABLE user CHANGE person_id person_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D3728193 FOREIGN KEY (person_id_id) REFERENCES person (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8D93D649D3728193 ON user (person_id_id)');
    }
}
