<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250430123549 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE categories DROP FOREIGN KEY FK_3AF34668727ACA70
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE categories ADD tree_root INT DEFAULT NULL, ADD lft INT NOT NULL, ADD lvl INT NOT NULL, ADD rgt INT NOT NULL, ADD url_title VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE categories ADD CONSTRAINT FK_3AF34668A977936C FOREIGN KEY (tree_root) REFERENCES `categories` (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE categories ADD CONSTRAINT FK_3AF34668727ACA70 FOREIGN KEY (parent_id) REFERENCES `categories` (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_3AF34668EE92171C ON categories (url_title)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3AF34668A977936C ON categories (tree_root)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE `categories` DROP FOREIGN KEY FK_3AF34668A977936C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `categories` DROP FOREIGN KEY FK_3AF34668727ACA70
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_3AF34668EE92171C ON `categories`
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_3AF34668A977936C ON `categories`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `categories` DROP tree_root, DROP lft, DROP lvl, DROP rgt, DROP url_title
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `categories` ADD CONSTRAINT FK_3AF34668727ACA70 FOREIGN KEY (parent_id) REFERENCES categories (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
    }
}
