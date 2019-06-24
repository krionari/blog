<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190624211029 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE people DROP FOREIGN KEY FK_28166A2650B678B7');
        $this->addSql('DROP TABLE hobbies');
        $this->addSql('DROP TABLE people');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE hobbies (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE people (id INT AUTO_INCREMENT NOT NULL, hobbie_id INT NOT NULL, firstname VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, lastname VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, pseudo VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, INDEX IDX_28166A2650B678B7 (hobbie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE people ADD CONSTRAINT FK_28166A2650B678B7 FOREIGN KEY (hobbie_id) REFERENCES hobbies (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
