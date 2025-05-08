<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250508120617 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE categories DROP FOREIGN KEY FK_3AF34668A977936C
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_3AF34668A977936C ON categories
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE categories CHANGE tree_root root INT DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE `categories` CHANGE root tree_root INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `categories` ADD CONSTRAINT FK_3AF34668A977936C FOREIGN KEY (tree_root) REFERENCES categories (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3AF34668A977936C ON `categories` (tree_root)
        SQL);
    }
}
