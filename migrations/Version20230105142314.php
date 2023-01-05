<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230105142314 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE EVENT `change_etat_clos` ON SCHEDULE EVERY 1 MINUTE STARTS \'2023-01-05 15:03:05.000000\' ENDS \'2024-01-12 15:03:05.000000\' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE sorties SET sorties.etat_id=3 WHERE sorties.date_debut > NOW() and sorties.date_cloture < NOW()');
        $this->addSql('CREATE EVENT `change_etat_cour` ON SCHEDULE EVERY 1 MINUTE STARTS \'2023-01-05 15:03:05.000000\' ENDS \'2024-01-12 15:03:05.000000\' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE sorties SET sorties.etat_id = 4 WHERE (sorties.etat_id=2 or sorties.etat_id=3) and sorties.date_debut<NOW() and NOW()<DATE_SUB(sorties.date_debut, INTERVAL -sorties.duree MINUTE)');
        $this->addSql('CREATE EVENT `change_etat_passe` ON SCHEDULE EVERY 1 MINUTE STARTS \'2023-01-05 15:03:05.000000\' ENDS \'2024-01-12 15:03:05.000000\' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE sorties SET sorties.etat_id = 5 WHERE (sorties.etat_id=2 or sorties.etat_id=3 or sorties.etat_id=4) and NOW()>DATE_SUB(sorties.date_debut, INTERVAL -sorties.duree MINUTE)');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
