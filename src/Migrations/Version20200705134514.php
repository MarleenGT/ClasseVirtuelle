<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200705134514 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE admins (id INT AUTO_INCREMENT NOT NULL, id_user_id INT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_A2E0150F79F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE admins ADD CONSTRAINT FK_A2E0150F79F37AE5 FOREIGN KEY (id_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE archives ADD CONSTRAINT FK_E262EC39755C5E8E FOREIGN KEY (id_prof_id) REFERENCES profs (id)');
        $this->addSql('ALTER TABLE archives ADD CONSTRAINT FK_E262EC39F6B192E FOREIGN KEY (id_classe_id) REFERENCES classes (id)');
        $this->addSql('ALTER TABLE archives ADD CONSTRAINT FK_E262EC39E2E395DC FOREIGN KEY (id_sousgroupe_id) REFERENCES sousgroupes (id)');
        $this->addSql('ALTER TABLE archives ADD CONSTRAINT FK_E262EC3951E6528F FOREIGN KEY (id_matiere_id) REFERENCES matieres (id)');
        $this->addSql('CREATE INDEX IDX_E262EC39755C5E8E ON archives (id_prof_id)');
        $this->addSql('CREATE INDEX IDX_E262EC39F6B192E ON archives (id_classe_id)');
        $this->addSql('CREATE INDEX IDX_E262EC39E2E395DC ON archives (id_sousgroupe_id)');
        $this->addSql('CREATE INDEX IDX_E262EC3951E6528F ON archives (id_matiere_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE admins');
        $this->addSql('ALTER TABLE archives DROP FOREIGN KEY FK_E262EC39755C5E8E');
        $this->addSql('ALTER TABLE archives DROP FOREIGN KEY FK_E262EC39F6B192E');
        $this->addSql('ALTER TABLE archives DROP FOREIGN KEY FK_E262EC39E2E395DC');
        $this->addSql('ALTER TABLE archives DROP FOREIGN KEY FK_E262EC3951E6528F');
        $this->addSql('DROP INDEX IDX_E262EC39755C5E8E ON archives');
        $this->addSql('DROP INDEX IDX_E262EC39F6B192E ON archives');
        $this->addSql('DROP INDEX IDX_E262EC39E2E395DC ON archives');
        $this->addSql('DROP INDEX IDX_E262EC3951E6528F ON archives');
    }
}
