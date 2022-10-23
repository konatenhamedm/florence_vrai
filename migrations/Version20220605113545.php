<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220605113545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE acte_vente_workflow (id INT AUTO_INCREMENT NOT NULL, acte_id INT DEFAULT NULL, workflow_id INT DEFAULT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, etat VARCHAR(255) NOT NULL, INDEX IDX_F1B5518EA767B8C7 (acte_id), INDEX IDX_F1B5518E2C7C2CBA (workflow_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gestion_workflow (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workflow (id INT AUTO_INCREMENT NOT NULL, type_acte_id INT DEFAULT NULL, gestion_workflow_id INT DEFAULT NULL, numero_etape VARCHAR(255) NOT NULL, libelle_etape VARCHAR(255) NOT NULL, nombre_jours INT NOT NULL, INDEX IDX_65C598168F46D732 (type_acte_id), INDEX IDX_65C59816D7F6FC50 (gestion_workflow_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE acte_vente_workflow ADD CONSTRAINT FK_F1B5518EA767B8C7 FOREIGN KEY (acte_id) REFERENCES acte (id)');
        $this->addSql('ALTER TABLE acte_vente_workflow ADD CONSTRAINT FK_F1B5518E2C7C2CBA FOREIGN KEY (workflow_id) REFERENCES workflow (id)');
        $this->addSql('ALTER TABLE workflow ADD CONSTRAINT FK_65C598168F46D732 FOREIGN KEY (type_acte_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE workflow ADD CONSTRAINT FK_65C59816D7F6FC50 FOREIGN KEY (gestion_workflow_id) REFERENCES gestion_workflow (id)');
        $this->addSql('ALTER TABLE acte ADD type_acte_id INT DEFAULT NULL, ADD numero_classification VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE acte ADD CONSTRAINT FK_9EC413268F46D732 FOREIGN KEY (type_acte_id) REFERENCES type (id)');
        $this->addSql('CREATE INDEX IDX_9EC413268F46D732 ON acte (type_acte_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE workflow DROP FOREIGN KEY FK_65C59816D7F6FC50');
        $this->addSql('ALTER TABLE acte_vente_workflow DROP FOREIGN KEY FK_F1B5518E2C7C2CBA');
        $this->addSql('DROP TABLE acte_vente_workflow');
        $this->addSql('DROP TABLE gestion_workflow');
        $this->addSql('DROP TABLE workflow');
        $this->addSql('ALTER TABLE acte DROP FOREIGN KEY FK_9EC413268F46D732');
        $this->addSql('DROP INDEX IDX_9EC413268F46D732 ON acte');
        $this->addSql('ALTER TABLE acte DROP type_acte_id, DROP numero_classification');
    }
}
