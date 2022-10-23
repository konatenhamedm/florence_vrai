<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220607174248 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE identification (id INT AUTO_INCREMENT NOT NULL, acheteur_id INT DEFAULT NULL, vendeur_id INT DEFAULT NULL, dossier_id INT DEFAULT NULL, INDEX IDX_49E7720D96A7BB5F (acheteur_id), INDEX IDX_49E7720D858C065E (vendeur_id), INDEX IDX_49E7720D611C0C56 (dossier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE piece (id INT AUTO_INCREMENT NOT NULL, dossier_id INT DEFAULT NULL, client_id INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, date_transmission DATETIME NOT NULL, INDEX IDX_44CA0B23611C0C56 (dossier_id), INDEX IDX_44CA0B2319EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE identification ADD CONSTRAINT FK_49E7720D96A7BB5F FOREIGN KEY (acheteur_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE identification ADD CONSTRAINT FK_49E7720D858C065E FOREIGN KEY (vendeur_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE identification ADD CONSTRAINT FK_49E7720D611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id)');
        $this->addSql('ALTER TABLE piece ADD CONSTRAINT FK_44CA0B23611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id)');
        $this->addSql('ALTER TABLE piece ADD CONSTRAINT FK_44CA0B2319EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE identification');
        $this->addSql('DROP TABLE piece');
    }
}
