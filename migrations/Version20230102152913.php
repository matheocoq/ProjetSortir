<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230102152913 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('INSERT INTO etats (libelle) VALUES ("Créée");');
        $this->addSql("INSERT INTO etats (libelle) VALUES ('Ouverte');");
        $this->addSql("INSERT INTO etats (libelle) VALUES ('Clôturée'); ");
        $this->addSql("INSERT INTO etats (libelle) VALUES ('Activité en cours'); ");
        $this->addSql("INSERT INTO etats (libelle) VALUES ('Passée'); ");
        $this->addSql("INSERT INTO etats (libelle) VALUES ('Annulée'); ");
        $this->addSql("INSERT INTO ville (nom,code_postal) VALUES ('Nantes','44000');");
        $this->addSql("INSERT INTO sites (nom) VALUES ('Eni Nantes'); ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
