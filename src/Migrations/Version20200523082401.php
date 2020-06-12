<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200523082401 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE classes (id INT AUTO_INCREMENT NOT NULL, nom_classe VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentaires (id INT AUTO_INCREMENT NOT NULL, id_concerne_id INT NOT NULL, id_auteur_id INT NOT NULL, date DATETIME NOT NULL, commentaire LONGTEXT NOT NULL, INDEX IDX_D9BEC0C47C143306 (id_concerne_id), INDEX IDX_D9BEC0C4E08ED3C1 (id_auteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cours (id INT AUTO_INCREMENT NOT NULL, id_classe_id INT DEFAULT NULL, id_sousgroupe_id INT DEFAULT NULL, id_prof_id INT NOT NULL, heure_debut DATETIME NOT NULL, heure_fin DATETIME NOT NULL, commentaire LONGTEXT DEFAULT NULL, INDEX IDX_FDCA8C9CF6B192E (id_classe_id), INDEX IDX_FDCA8C9CE2E395DC (id_sousgroupe_id), INDEX IDX_FDCA8C9C755C5E8E (id_prof_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE droits (id INT AUTO_INCREMENT NOT NULL, nom_droit VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE eleves (id INT AUTO_INCREMENT NOT NULL, id_user_id INT NOT NULL, id_classe_id INT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_383B09B179F37AE5 (id_user_id), INDEX IDX_383B09B1F6B192E (id_classe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE eleves_sousgroupes (eleves_id INT NOT NULL, sousgroupes_id INT NOT NULL, INDEX IDX_B10EF158C2140342 (eleves_id), INDEX IDX_B10EF15855907D49 (sousgroupes_id), PRIMARY KEY(eleves_id, sousgroupes_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE matieres (id INT AUTO_INCREMENT NOT NULL, nom_matiere VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personnels (id INT AUTO_INCREMENT NOT NULL, id_user_id INT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_7AC38C2B79F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profs (id INT AUTO_INCREMENT NOT NULL, id_user_id INT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_47E61F7F79F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profs_matieres (profs_id INT NOT NULL, matieres_id INT NOT NULL, INDEX IDX_5BFBB0C8BDDFA3C9 (profs_id), INDEX IDX_5BFBB0C882350831 (matieres_id), PRIMARY KEY(profs_id, matieres_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roles (id INT AUTO_INCREMENT NOT NULL, nom_role VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sousgroupes (id INT AUTO_INCREMENT NOT NULL, id_createur_id INT NOT NULL, INDEX IDX_7E4F73AD6BB0CC12 (id_createur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, id_role_id INT NOT NULL, identifiant VARCHAR(255) NOT NULL, mdp VARCHAR(255) NOT NULL, INDEX IDX_1483A5E989E8BDC (id_role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commentaires ADD CONSTRAINT FK_D9BEC0C47C143306 FOREIGN KEY (id_concerne_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE commentaires ADD CONSTRAINT FK_D9BEC0C4E08ED3C1 FOREIGN KEY (id_auteur_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9CF6B192E FOREIGN KEY (id_classe_id) REFERENCES classes (id)');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9CE2E395DC FOREIGN KEY (id_sousgroupe_id) REFERENCES sousgroupes (id)');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C755C5E8E FOREIGN KEY (id_prof_id) REFERENCES profs (id)');
        $this->addSql('ALTER TABLE eleves ADD CONSTRAINT FK_383B09B179F37AE5 FOREIGN KEY (id_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE eleves ADD CONSTRAINT FK_383B09B1F6B192E FOREIGN KEY (id_classe_id) REFERENCES classes (id)');
        $this->addSql('ALTER TABLE eleves_sousgroupes ADD CONSTRAINT FK_B10EF158C2140342 FOREIGN KEY (eleves_id) REFERENCES eleves (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE eleves_sousgroupes ADD CONSTRAINT FK_B10EF15855907D49 FOREIGN KEY (sousgroupes_id) REFERENCES sousgroupes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE personnels ADD CONSTRAINT FK_7AC38C2B79F37AE5 FOREIGN KEY (id_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE profs ADD CONSTRAINT FK_47E61F7F79F37AE5 FOREIGN KEY (id_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE profs_matieres ADD CONSTRAINT FK_5BFBB0C8BDDFA3C9 FOREIGN KEY (profs_id) REFERENCES profs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE profs_matieres ADD CONSTRAINT FK_5BFBB0C882350831 FOREIGN KEY (matieres_id) REFERENCES matieres (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sousgroupes ADD CONSTRAINT FK_7E4F73AD6BB0CC12 FOREIGN KEY (id_createur_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E989E8BDC FOREIGN KEY (id_role_id) REFERENCES roles (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9CF6B192E');
        $this->addSql('ALTER TABLE eleves DROP FOREIGN KEY FK_383B09B1F6B192E');
        $this->addSql('ALTER TABLE eleves_sousgroupes DROP FOREIGN KEY FK_B10EF158C2140342');
        $this->addSql('ALTER TABLE profs_matieres DROP FOREIGN KEY FK_5BFBB0C882350831');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C755C5E8E');
        $this->addSql('ALTER TABLE profs_matieres DROP FOREIGN KEY FK_5BFBB0C8BDDFA3C9');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E989E8BDC');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9CE2E395DC');
        $this->addSql('ALTER TABLE eleves_sousgroupes DROP FOREIGN KEY FK_B10EF15855907D49');
        $this->addSql('ALTER TABLE commentaires DROP FOREIGN KEY FK_D9BEC0C47C143306');
        $this->addSql('ALTER TABLE commentaires DROP FOREIGN KEY FK_D9BEC0C4E08ED3C1');
        $this->addSql('ALTER TABLE eleves DROP FOREIGN KEY FK_383B09B179F37AE5');
        $this->addSql('ALTER TABLE personnels DROP FOREIGN KEY FK_7AC38C2B79F37AE5');
        $this->addSql('ALTER TABLE profs DROP FOREIGN KEY FK_47E61F7F79F37AE5');
        $this->addSql('ALTER TABLE sousgroupes DROP FOREIGN KEY FK_7E4F73AD6BB0CC12');
        $this->addSql('DROP TABLE classes');
        $this->addSql('DROP TABLE commentaires');
        $this->addSql('DROP TABLE cours');
        $this->addSql('DROP TABLE droits');
        $this->addSql('DROP TABLE eleves');
        $this->addSql('DROP TABLE eleves_sousgroupes');
        $this->addSql('DROP TABLE matieres');
        $this->addSql('DROP TABLE personnels');
        $this->addSql('DROP TABLE profs');
        $this->addSql('DROP TABLE profs_matieres');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE sousgroupes');
        $this->addSql('DROP TABLE users');
    }
}
