<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160209144534 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE query_request CHANGE dateCreated dateCreated DATETIME NOT NULL, CHANGE dateLastModified dateLastModified DATETIME NOT NULL');
        $this->addSql('ALTER TABLE query CHANGE dateCreated dateCreated DATETIME NOT NULL');
        $this->addSql('ALTER TABLE chart_config DROP nameOfXAxis, CHANGE fetched_on fetched_on DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE chart_config ADD nameOfXAxis VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE fetched_on fetched_on DATETIME NOT NULL');
        $this->addSql('ALTER TABLE query CHANGE dateCreated dateCreated DATETIME NOT NULL');
        $this->addSql('ALTER TABLE query_request CHANGE dateCreated dateCreated DATETIME NOT NULL, CHANGE dateLastModified dateLastModified DATETIME NOT NULL');
    }
}
