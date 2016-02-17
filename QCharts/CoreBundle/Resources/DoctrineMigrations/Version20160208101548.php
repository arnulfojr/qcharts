<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160208101548 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE favorites_user (query_request_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_1650483B89E96725 (query_request_id), INDEX IDX_1650483BA76ED395 (user_id), PRIMARY KEY(query_request_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE favorites_user ADD CONSTRAINT FK_1650483B89E96725 FOREIGN KEY (query_request_id) REFERENCES query_request (id)');
        $this->addSql('ALTER TABLE favorites_user ADD CONSTRAINT FK_1650483BA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE query_request CHANGE dateCreated dateCreated DATETIME NOT NULL, CHANGE dateLastModified dateLastModified DATETIME NOT NULL');
        $this->addSql('ALTER TABLE query CHANGE dateCreated dateCreated DATETIME NOT NULL');
        $this->addSql('ALTER TABLE chart_config CHANGE fetched_on fetched_on DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE favorites_user');
        $this->addSql('ALTER TABLE chart_config CHANGE fetched_on fetched_on DATETIME NOT NULL');
        $this->addSql('ALTER TABLE query CHANGE dateCreated dateCreated DATETIME NOT NULL');
        $this->addSql('ALTER TABLE query_request CHANGE dateCreated dateCreated DATETIME NOT NULL, CHANGE dateLastModified dateLastModified DATETIME NOT NULL');
    }
}
