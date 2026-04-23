<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260423074253 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création de l\'entité CategorieGalerie et ajout de la relation categorie_id sur Galerie.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie_galerie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE galerie ADD categorie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE galerie ADD CONSTRAINT FK_9E7D1590BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie_galerie (id)');
        $this->addSql('CREATE INDEX IDX_9E7D1590BCF5E72D ON galerie (categorie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE categorie_galerie');
        $this->addSql('ALTER TABLE galerie DROP FOREIGN KEY FK_9E7D1590BCF5E72D');
        $this->addSql('DROP INDEX IDX_9E7D1590BCF5E72D ON galerie');
        $this->addSql('ALTER TABLE galerie DROP categorie_id');
    }
}
