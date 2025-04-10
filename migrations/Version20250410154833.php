<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250410154833 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC50EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC9726CAE0 FOREIGN KEY (id_recette) REFERENCES recette (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_67F068BC50EAE44 ON commentaire (id_utilisateur)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_67F068BC9726CAE0 ON commentaire (id_recette)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detenir MODIFY id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `primary` ON detenir
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detenir DROP id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detenir ADD CONSTRAINT FK_F27B4E8B9726CAE0 FOREIGN KEY (id_recette) REFERENCES recette (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detenir ADD CONSTRAINT FK_F27B4E8BCE25F8A7 FOREIGN KEY (id_ingredient) REFERENCES ingredient (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F27B4E8B9726CAE0 ON detenir (id_recette)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F27B4E8BCE25F8A7 ON detenir (id_ingredient)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detenir ADD PRIMARY KEY (id_recette, id_ingredient)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etape ADD CONSTRAINT FK_285F75DD9726CAE0 FOREIGN KEY (id_recette) REFERENCES recette (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_285F75DD9726CAE0 ON etape (id_recette)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE note ADD CONSTRAINT FK_CFBDFA1450EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE note ADD CONSTRAINT FK_CFBDFA149726CAE0 FOREIGN KEY (id_recette) REFERENCES recette (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_CFBDFA1450EAE44 ON note (id_utilisateur)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_CFBDFA149726CAE0 ON note (id_recette)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recette ADD CONSTRAINT FK_49BB639050EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_49BB639050EAE44 ON recette (id_utilisateur)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utiliser MODIFY id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `primary` ON utiliser
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utiliser DROP id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utiliser ADD CONSTRAINT FK_5C9491099726CAE0 FOREIGN KEY (id_recette) REFERENCES recette (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utiliser ADD CONSTRAINT FK_5C94910952D7553C FOREIGN KEY (id_ustensile) REFERENCES ustensile (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5C9491099726CAE0 ON utiliser (id_recette)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5C94910952D7553C ON utiliser (id_ustensile)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utiliser ADD PRIMARY KEY (id_recette, id_ustensile)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC50EAE44
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC9726CAE0
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_67F068BC50EAE44 ON commentaire
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_67F068BC9726CAE0 ON commentaire
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utiliser DROP FOREIGN KEY FK_5C9491099726CAE0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utiliser DROP FOREIGN KEY FK_5C94910952D7553C
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_5C9491099726CAE0 ON utiliser
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_5C94910952D7553C ON utiliser
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utiliser ADD id INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detenir DROP FOREIGN KEY FK_F27B4E8B9726CAE0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detenir DROP FOREIGN KEY FK_F27B4E8BCE25F8A7
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_F27B4E8B9726CAE0 ON detenir
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_F27B4E8BCE25F8A7 ON detenir
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detenir ADD id INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etape DROP FOREIGN KEY FK_285F75DD9726CAE0
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_285F75DD9726CAE0 ON etape
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recette DROP FOREIGN KEY FK_49BB639050EAE44
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_49BB639050EAE44 ON recette
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA1450EAE44
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA149726CAE0
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_CFBDFA1450EAE44 ON note
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_CFBDFA149726CAE0 ON note
        SQL);
    }
}
