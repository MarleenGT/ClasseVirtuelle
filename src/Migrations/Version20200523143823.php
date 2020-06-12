<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200523143823 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE profs_classes (profs_id INT NOT NULL, classes_id INT NOT NULL, INDEX IDX_B88D8C65BDDFA3C9 (profs_id), INDEX IDX_B88D8C659E225B24 (classes_id), PRIMARY KEY(profs_id, classes_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE profs_classes ADD CONSTRAINT FK_B88D8C65BDDFA3C9 FOREIGN KEY (profs_id) REFERENCES profs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE profs_classes ADD CONSTRAINT FK_B88D8C659E225B24 FOREIGN KEY (classes_id) REFERENCES classes (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9C90409EC ON users (identifiant)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE profs_classes');
        $this->addSql('DROP INDEX UNIQ_1483A5E9C90409EC ON users');
        $this->addSql('DROP INDEX UNIQ_1483A5E9E7927C74 ON users');
    }
}
