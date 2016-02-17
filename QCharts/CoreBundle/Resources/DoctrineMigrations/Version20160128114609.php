<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160128114609 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE query_request ADD directory_id INT DEFAULT NULL, CHANGE dateCreated dateCreated DATETIME NOT NULL, CHANGE dateLastModified dateLastModified DATETIME NOT NULL');
        $this->addSql('ALTER TABLE query_request ADD CONSTRAINT FK_713DBEBD2C94069F FOREIGN KEY (directory_id) REFERENCES directory (id)');
        $this->addSql('CREATE INDEX IDX_713DBEBD2C94069F ON query_request (directory_id)');
        $this->addSql('ALTER TABLE query CHANGE dateCreated dateCreated DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE query CHANGE dateCreated dateCreated DATETIME NOT NULL');
        $this->addSql('ALTER TABLE query_request DROP FOREIGN KEY FK_713DBEBD2C94069F');
        $this->addSql('DROP INDEX IDX_713DBEBD2C94069F ON query_request');
        $this->addSql('ALTER TABLE query_request DROP directory_id, CHANGE dateCreated dateCreated DATETIME NOT NULL, CHANGE dateLastModified dateLastModified DATETIME NOT NULL');
    }
}
