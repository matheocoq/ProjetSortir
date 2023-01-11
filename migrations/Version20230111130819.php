<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230111130819 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO ville (id,nom,code_postal) VALUES (2,'Paris ','75000');");
        $this->addSql("INSERT INTO ville (id,nom,code_postal) VALUES (3,'Bordeaux','33000');");

        $this->addSql("INSERT INTO sites (id,nom) VALUES (2,'Rennes');");
        $this->addSql("INSERT INTO sites (id,nom) VALUES (3,'Niort');");

        $this->addSql("INSERT INTO lieux (id, ville_id, nom, rue, latitude, longitude) VALUES (1, 1, 'Les Machines de l\'Île', 'Parc des Chantiers, Bd Léon Bureau', 47.20632195508921, -1.5644108070020861);");
        $this->addSql("INSERT INTO lieux (id, ville_id, nom, rue, latitude, longitude) VALUES (2, 2, 'Tour Eiffel', 'Champ de Mars, 5 Av. Anatole France', 48.85852182064514, 2.294534950654419);");
        $this->addSql("INSERT INTO lieux (id, ville_id, nom, rue, latitude, longitude) VALUES (3, 1, 'Opéra National de Bordeaux - Grand-Théâtre', 'Pl. de la Comédie', 44.84272388488576, -0.5741881665253703);");
       
       
        $this->addSql("INSERT INTO user (id, email, roles, password, nom, prenom, pseudo, telephone, sites_id, administrateur, actif, image) VALUES (1, 'martin@dupont.fr', '[]', '$2y$10\$vK34O.Li4lakVImSMAlwZulBSeFMXN5y9SrW4m2HTcV1W/.84DGEm', 'Dupont', 'Martin', 'martin.dupont', '0601344258', 1, 0, 1, NULL);");
        $this->addSql("INSERT INTO user (id, email, roles, password, nom, prenom, pseudo, telephone, sites_id, administrateur, actif, image) VALUES (2, 'john.doe@gmail.com', '[]', '$2y$10\$vK34O.Li4lakVImSMAlwZulBSeFMXN5y9SrW4m2HTcV1W/.84DGEm', 'Doe', 'John', 'John.Doe', '0687744785', 2, 0, 1, NULL);");
        $this->addSql("INSERT INTO user (id, email, roles, password, nom, prenom, pseudo, telephone, sites_id, administrateur, actif, image) VALUES (3, 'louise.carcier@hotmail.com', '[]', '$2y$10\$vK34O.Li4lakVImSMAlwZulBSeFMXN5y9SrW4m2HTcV1W/.84DGEm', 'Carcier', 'Louise', 'louise.carcier', '0654457871', 3, 0, 1, NULL);");
        $this->addSql("INSERT INTO user (id, email, roles, password, nom, prenom, pseudo, telephone, sites_id, administrateur, actif, image) VALUES (4, 'sortir.admin@mail.com', '[\"ROLE_ADMIN\"]', '$2y$10\$vK34O.Li4lakVImSMAlwZulBSeFMXN5y9SrW4m2HTcV1W/.84DGEm', 'Sortir', 'Admin', 'sortir.admin', '0254487785', 1, 1, 1, NULL);");
        $this->addSql("INSERT INTO user (id, email, roles, password, nom, prenom, pseudo, telephone, sites_id, administrateur, actif, image) VALUES (5, 'gus.fring@lospolloshermanos.fr', '[]', '$2y$10\$vK34O.Li4lakVImSMAlwZulBSeFMXN5y9SrW4m2HTcV1W/.84DGEm', 'Fring', 'Gus', 'Gus.Fring', '0645787454', 2, 0, 1, NULL);");
       
        $this->addSql("INSERT INTO sorties (id, organisateur_id, lieux_id, etat_id, nom, date_debut, duree, date_cloture, nb_inscription_max, description, url_photo) VALUES (1, 1, 1, 1, 'Visite des machines de l\'île de Nantes', '2023-01-18 14:20:47', '90', '2023-01-17 14:20:47', 20, NULL, NULL);");
        $this->addSql("INSERT INTO sorties (id, organisateur_id, lieux_id, etat_id, nom, date_debut, duree, date_cloture, nb_inscription_max, description, url_photo) VALUES (2, 2, 2, 2, 'Visite de la tour Eiffel', '2023-01-18 14:22:49', '90', '2023-01-17 14:22:49', 20, NULL, NULL);");
        $this->addSql("INSERT INTO sorties (id, organisateur_id, lieux_id, etat_id, nom, date_debut, duree, date_cloture, nb_inscription_max, description, url_photo) VALUES (3, 1, 3, 3, 'Visite de l\'opéra de Bordeaux', '2023-01-18 14:24:25', '90', '2023-01-10 14:24:25', 20, NULL, NULL);");
        $this->addSql("INSERT INTO sorties (id, organisateur_id, lieux_id, etat_id, nom, date_debut, duree, date_cloture, nb_inscription_max, description, url_photo) VALUES (4, 3, 1, 5, 'Visite des machine de l\'île de Nantes', '2023-01-04 14:26:04', '90', '2023-01-03 14:26:04', 20, NULL, NULL);");
        $this->addSql("INSERT INTO sorties (id, organisateur_id, lieux_id, etat_id, nom, date_debut, duree, date_cloture, nb_inscription_max, description, url_photo) VALUES (5, 1, 2, 6, 'Visite de la tour Eiffel', '2023-01-18 14:22:49', '90', '2023-01-17 14:22:49', 20, NULL, NULL);");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
