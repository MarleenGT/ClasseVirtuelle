<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200523090133 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE droits_roles (droits_id INT NOT NULL, roles_id INT NOT NULL, INDEX IDX_5EBD240CB72E652A (droits_id), INDEX IDX_5EBD240C38C751C4 (roles_id), PRIMARY KEY(droits_id, roles_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE droits_roles ADD CONSTRAINT FK_5EBD240CB72E652A FOREIGN KEY (droits_id) REFERENCES droits (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE droits_roles ADD CONSTRAINT FK_5EBD240C38C751C4 FOREIGN KEY (roles_id) REFERENCES roles (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE droits_roles');
    }
}
