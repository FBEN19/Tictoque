<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250417091019 extends AbstractMigration
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
            DROP INDEX `primary` ON etape
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etape ADD PRIMARY KEY (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE etape MODIFY id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `PRIMARY` ON etape
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etape ADD PRIMARY KEY (id, recette_id)
        SQL);
    }
}
