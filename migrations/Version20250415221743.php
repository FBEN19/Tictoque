<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250415221743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE etape MODIFY id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etape DROP FOREIGN KEY FK_285F75DD9726CAE0
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_285F75DD9726CAE0 ON etape
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `primary` ON etape
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etape CHANGE id id INT NOT NULL, CHANGE id_recette recette_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etape ADD CONSTRAINT FK_285F75DD89312FE9 FOREIGN KEY (recette_id) REFERENCES recette (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_285F75DD89312FE9 ON etape (recette_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etape ADD PRIMARY KEY (id, recette_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE etape DROP FOREIGN KEY FK_285F75DD89312FE9
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_285F75DD89312FE9 ON etape
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `PRIMARY` ON etape
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etape CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE recette_id id_recette INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etape ADD CONSTRAINT FK_285F75DD9726CAE0 FOREIGN KEY (id_recette) REFERENCES recette (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_285F75DD9726CAE0 ON etape (id_recette)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etape ADD PRIMARY KEY (id)
        SQL);
    }
}
