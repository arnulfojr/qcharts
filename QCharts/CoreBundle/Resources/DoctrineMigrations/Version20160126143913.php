<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160126143913 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE directory (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_467844DA727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE directory ADD CONSTRAINT FK_467844DA727ACA70 FOREIGN KEY (parent_id) REFERENCES directory (id)');
        $this->addSql('ALTER TABLE query_request CHANGE dateCreated dateCreated DATETIME NOT NULL, CHANGE dateLastModified dateLastModified DATETIME NOT NULL');
        $this->addSql('ALTER TABLE query CHANGE dateCreated dateCreated DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE directory DROP FOREIGN KEY FK_467844DA727ACA70');
        $this->addSql('DROP TABLE directory');
        $this->addSql('ALTER TABLE query CHANGE dateCreated dateCreated DATETIME NOT NULL');
        $this->addSql('ALTER TABLE query_request CHANGE dateCreated dateCreated DATETIME NOT NULL, CHANGE dateLastModified dateLastModified DATETIME NOT NULL');
    }
}
