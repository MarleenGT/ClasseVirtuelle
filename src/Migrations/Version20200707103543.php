<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200707103543 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sousgroupes_users (sousgroupes_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_1853DC6B55907D49 (sousgroupes_id), INDEX IDX_1853DC6B67B3B43D (users_id), PRIMARY KEY(sousgroupes_id, users_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sousgroupes_users ADD CONSTRAINT FK_1853DC6B55907D49 FOREIGN KEY (sousgroupes_id) REFERENCES sousgroupes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sousgroupes_users ADD CONSTRAINT FK_1853DC6B67B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sousgroupes_users');
    }
}
