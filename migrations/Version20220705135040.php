<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220705135040 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE acte_vente_workflow DROP FOREIGN KEY FK_F1B5518EA767B8C7');
        $this->addSql('ALTER TABLE archive DROP FOREIGN KEY FK_D5FC5D9CA767B8C7');
        $this->addSql('ALTER TABLE fichier_acte DROP FOREIGN KEY FK_1F4BDBCDA767B8C7');
        $this->addSql('ALTER TABLE document_signe1 DROP FOREIGN KEY FK_BBD8CCDE611C0C56');
        $this->addSql('ALTER TABLE enregistrement1 DROP FOREIGN KEY FK_280C8D4E611C0C56');
        $this->addSql('ALTER TABLE identification1 DROP FOREIGN KEY FK_FD247478611C0C56');
        $this->addSql('ALTER TABLE piece1 DROP FOREIGN KEY FK_21FF54CE611C0C56');
        $this->addSql('ALTER TABLE document_type_acte DROP FOREIGN KEY FK_5BF3FE8FC33F7837');
        $this->addSql('DROP TABLE acte');
        $this->addSql('DROP TABLE acte_vente_workflow');
        $this->addSql('DROP TABLE document_signe1');
        $this->addSql('DROP TABLE dossier_acte');
        $this->addSql('DROP TABLE enregistrement1');
        $this->addSql('DROP TABLE gestion_type_acte');
        $this->addSql('DROP TABLE identification1');
        $this->addSql('DROP TABLE piece1');
        $this->addSql('DROP INDEX IDX_D5FC5D9CA767B8C7 ON archive');
        $this->addSql('ALTER TABLE archive DROP acte_id');
        $this->addSql('ALTER TABLE document_signe ADD fichier_id INT DEFAULT NULL, ADD document_id INT NOT NULL, DROP libelle, DROP path');
        $this->addSql('ALTER TABLE document_signe ADD CONSTRAINT FK_DCE1ADA2F915CFE FOREIGN KEY (fichier_id) REFERENCES fichier (id)');
        $this->addSql('ALTER TABLE document_signe ADD CONSTRAINT FK_DCE1ADA2C33F7837 FOREIGN KEY (document_id) REFERENCES document_type_acte (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DCE1ADA2F915CFE ON document_signe (fichier_id)');
        $this->addSql('CREATE INDEX IDX_DCE1ADA2C33F7837 ON document_signe (document_id)');
        $this->addSql('DROP INDEX IDX_5BF3FE8FC33F7837 ON document_type_acte');
        $this->addSql('ALTER TABLE document_type_acte ADD etapes JSON NOT NULL, DROP document_id');
        $this->addSql('ALTER TABLE enregistrement ADD fichier_id INT DEFAULT NULL, DROP path, CHANGE date_retour date_retour DATETIME NOT NULL');
        $this->addSql('ALTER TABLE enregistrement ADD CONSTRAINT FK_15FA02FF915CFE FOREIGN KEY (fichier_id) REFERENCES fichier (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_15FA02FF915CFE ON enregistrement (fichier_id)');
        $this->addSql('ALTER TABLE fichier DROP FOREIGN KEY FK_9B76551FF4028648');
        $this->addSql('DROP INDEX IDX_9B76551FF4028648 ON fichier');
        $this->addSql('ALTER TABLE fichier ADD url VARCHAR(25) NOT NULL, ADD date DATETIME NOT NULL, ADD size INT NOT NULL, DROP arrive_id, CHANGE titre alt VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX IDX_1F4BDBCDA767B8C7 ON fichier_acte');
        $this->addSql('ALTER TABLE fichier_acte DROP acte_id');
        $this->addSql('ALTER TABLE obtention ADD fichier_id INT DEFAULT NULL, DROP path');
        $this->addSql('ALTER TABLE obtention ADD CONSTRAINT FK_7A35A2B3F915CFE FOREIGN KEY (fichier_id) REFERENCES fichier (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7A35A2B3F915CFE ON obtention (fichier_id)');
        $this->addSql('ALTER TABLE piece DROP FOREIGN KEY FK_44CA0B2319EB6921');
        $this->addSql('DROP INDEX IDX_44CA0B2319EB6921 ON piece');
        $this->addSql('ALTER TABLE piece ADD document_id INT NOT NULL, ADD origine SMALLINT NOT NULL, DROP libelle, DROP path, CHANGE client_id fichier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE piece ADD CONSTRAINT FK_44CA0B23F915CFE FOREIGN KEY (fichier_id) REFERENCES fichier (id)');
        $this->addSql('ALTER TABLE piece ADD CONSTRAINT FK_44CA0B23C33F7837 FOREIGN KEY (document_id) REFERENCES document_type_acte (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_44CA0B23F915CFE ON piece (fichier_id)');
        $this->addSql('CREATE INDEX IDX_44CA0B23C33F7837 ON piece (document_id)');
        $this->addSql('ALTER TABLE redaction ADD fichier_id INT DEFAULT NULL, DROP path');
        $this->addSql('ALTER TABLE redaction ADD CONSTRAINT FK_29A014E5F915CFE FOREIGN KEY (fichier_id) REFERENCES fichier (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_29A014E5F915CFE ON redaction (fichier_id)');
        $this->addSql('ALTER TABLE remise ADD fichier_id INT NOT NULL');
        $this->addSql('ALTER TABLE remise ADD CONSTRAINT FK_117A95C7F915CFE FOREIGN KEY (fichier_id) REFERENCES fichier (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_117A95C7F915CFE ON remise (fichier_id)');
        $this->addSql('ALTER TABLE type ADD code VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE workflow ADD propriete VARCHAR(25) NOT NULL, ADD route VARCHAR(50) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE acte (id INT AUTO_INCREMENT NOT NULL, vendeur_id INT DEFAULT NULL, acheteur_id INT DEFAULT NULL, type_acte_id INT DEFAULT NULL, date DATETIME DEFAULT NULL, objet VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, montant DOUBLE PRECISION NOT NULL, active VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, numero VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, detail LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, details LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, etat_bien VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, numero_classification VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_9EC4132696A7BB5F (acheteur_id), INDEX IDX_9EC41326858C065E (vendeur_id), INDEX IDX_9EC413268F46D732 (type_acte_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE acte_vente_workflow (id INT AUTO_INCREMENT NOT NULL, acte_id INT DEFAULT NULL, workflow_id INT DEFAULT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, etat VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_F1B5518E2C7C2CBA (workflow_id), INDEX IDX_F1B5518EA767B8C7 (acte_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE document_signe1 (id INT AUTO_INCREMENT NOT NULL, dossier_id INT DEFAULT NULL, libelle VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_BBD8CCDE611C0C56 (dossier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE dossier_acte (id INT AUTO_INCREMENT NOT NULL, numero_ouverture VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, numero_classification VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE enregistrement1 (id INT AUTO_INCREMENT NOT NULL, dossier_id INT DEFAULT NULL, libelle VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_280C8D4E611C0C56 (dossier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE gestion_type_acte (id INT AUTO_INCREMENT NOT NULL, active VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE identification1 (id INT AUTO_INCREMENT NOT NULL, dossier_id INT DEFAULT NULL, acheteur_id INT DEFAULT NULL, vendeur_id INT DEFAULT NULL, INDEX IDX_FD24747896A7BB5F (acheteur_id), UNIQUE INDEX UNIQ_FD247478611C0C56 (dossier_id), INDEX IDX_FD247478858C065E (vendeur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE piece1 (id INT AUTO_INCREMENT NOT NULL, dossier_id INT DEFAULT NULL, libelle VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_21FF54CE611C0C56 (dossier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE acte ADD CONSTRAINT FK_9EC41326858C065E FOREIGN KEY (vendeur_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE acte ADD CONSTRAINT FK_9EC4132696A7BB5F FOREIGN KEY (acheteur_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE acte ADD CONSTRAINT FK_9EC413268F46D732 FOREIGN KEY (type_acte_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE acte_vente_workflow ADD CONSTRAINT FK_F1B5518E2C7C2CBA FOREIGN KEY (workflow_id) REFERENCES workflow (id)');
        $this->addSql('ALTER TABLE acte_vente_workflow ADD CONSTRAINT FK_F1B5518EA767B8C7 FOREIGN KEY (acte_id) REFERENCES acte (id)');
        $this->addSql('ALTER TABLE document_signe1 ADD CONSTRAINT FK_BBD8CCDE611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier_acte (id)');
        $this->addSql('ALTER TABLE enregistrement1 ADD CONSTRAINT FK_280C8D4E611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier_acte (id)');
        $this->addSql('ALTER TABLE identification1 ADD CONSTRAINT FK_FD247478611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier_acte (id)');
        $this->addSql('ALTER TABLE identification1 ADD CONSTRAINT FK_FD24747896A7BB5F FOREIGN KEY (acheteur_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE identification1 ADD CONSTRAINT FK_FD247478858C065E FOREIGN KEY (vendeur_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE piece1 ADD CONSTRAINT FK_21FF54CE611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier_acte (id)');
        $this->addSql('ALTER TABLE archive ADD acte_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE archive ADD CONSTRAINT FK_D5FC5D9CA767B8C7 FOREIGN KEY (acte_id) REFERENCES acte (id)');
        $this->addSql('CREATE INDEX IDX_D5FC5D9CA767B8C7 ON archive (acte_id)');
        $this->addSql('ALTER TABLE document_signe DROP FOREIGN KEY FK_DCE1ADA2F915CFE');
        $this->addSql('ALTER TABLE document_signe DROP FOREIGN KEY FK_DCE1ADA2C33F7837');
        $this->addSql('DROP INDEX UNIQ_DCE1ADA2F915CFE ON document_signe');
        $this->addSql('DROP INDEX IDX_DCE1ADA2C33F7837 ON document_signe');
        $this->addSql('ALTER TABLE document_signe ADD libelle VARCHAR(255) NOT NULL, ADD path VARCHAR(255) NOT NULL, DROP fichier_id, DROP document_id');
        $this->addSql('ALTER TABLE document_type_acte ADD document_id INT DEFAULT NULL, DROP etapes');
        $this->addSql('ALTER TABLE document_type_acte ADD CONSTRAINT FK_5BF3FE8FC33F7837 FOREIGN KEY (document_id) REFERENCES gestion_type_acte (id)');
        $this->addSql('CREATE INDEX IDX_5BF3FE8FC33F7837 ON document_type_acte (document_id)');
        $this->addSql('ALTER TABLE enregistrement DROP FOREIGN KEY FK_15FA02FF915CFE');
        $this->addSql('DROP INDEX UNIQ_15FA02FF915CFE ON enregistrement');
        $this->addSql('ALTER TABLE enregistrement ADD path VARCHAR(255) DEFAULT NULL, DROP fichier_id, CHANGE date_retour date_retour VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fichier ADD arrive_id INT DEFAULT NULL, DROP url, DROP date, DROP size, CHANGE alt titre VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE fichier ADD CONSTRAINT FK_9B76551FF4028648 FOREIGN KEY (arrive_id) REFERENCES courier_arrive (id)');
        $this->addSql('CREATE INDEX IDX_9B76551FF4028648 ON fichier (arrive_id)');
        $this->addSql('ALTER TABLE fichier_acte ADD acte_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fichier_acte ADD CONSTRAINT FK_1F4BDBCDA767B8C7 FOREIGN KEY (acte_id) REFERENCES acte (id)');
        $this->addSql('CREATE INDEX IDX_1F4BDBCDA767B8C7 ON fichier_acte (acte_id)');
        $this->addSql('ALTER TABLE obtention DROP FOREIGN KEY FK_7A35A2B3F915CFE');
        $this->addSql('DROP INDEX UNIQ_7A35A2B3F915CFE ON obtention');
        $this->addSql('ALTER TABLE obtention ADD path VARCHAR(255) DEFAULT NULL, DROP fichier_id');
        $this->addSql('ALTER TABLE piece DROP FOREIGN KEY FK_44CA0B23F915CFE');
        $this->addSql('ALTER TABLE piece DROP FOREIGN KEY FK_44CA0B23C33F7837');
        $this->addSql('DROP INDEX UNIQ_44CA0B23F915CFE ON piece');
        $this->addSql('DROP INDEX IDX_44CA0B23C33F7837 ON piece');
        $this->addSql('ALTER TABLE piece ADD libelle VARCHAR(255) DEFAULT NULL, ADD path VARCHAR(255) DEFAULT NULL, DROP document_id, DROP origine, CHANGE fichier_id client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE piece ADD CONSTRAINT FK_44CA0B2319EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_44CA0B2319EB6921 ON piece (client_id)');
        $this->addSql('ALTER TABLE redaction DROP FOREIGN KEY FK_29A014E5F915CFE');
        $this->addSql('DROP INDEX UNIQ_29A014E5F915CFE ON redaction');
        $this->addSql('ALTER TABLE redaction ADD path VARCHAR(255) DEFAULT NULL, DROP fichier_id');
        $this->addSql('ALTER TABLE remise DROP FOREIGN KEY FK_117A95C7F915CFE');
        $this->addSql('DROP INDEX UNIQ_117A95C7F915CFE ON remise');
        $this->addSql('ALTER TABLE remise DROP fichier_id');
        $this->addSql('ALTER TABLE type DROP code');
        $this->addSql('ALTER TABLE workflow DROP propriete, DROP route');
    }
}
