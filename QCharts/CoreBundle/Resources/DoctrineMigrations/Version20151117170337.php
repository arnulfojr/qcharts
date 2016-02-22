<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151117170337 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE query CHANGE dateCreated dateCreated DATETIME NOT NULL');
        $this->addSql('ALTER TABLE query_request ADD query_id INT DEFAULT NULL, CHANGE dateCreated dateCreated DATETIME NOT NULL');
        $this->addSql('ALTER TABLE query_request ADD CONSTRAINT FK_713DBEBDEF946F99 FOREIGN KEY (query_id) REFERENCES query (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_713DBEBDEF946F99 ON query_request (query_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE query CHANGE dateCreated dateCreated DATETIME NOT NULL');
        $this->addSql('ALTER TABLE query_request DROP FOREIGN KEY FK_713DBEBDEF946F99');
        $this->addSql('DROP INDEX UNIQ_713DBEBDEF946F99 ON query_request');
        $this->addSql('ALTER TABLE query_request DROP query_id, CHANGE dateCreated dateCreated DATETIME NOT NULL');
    }
}
