<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250418151209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A9D066842
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_B3BA5A5A9D066842 ON products
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE products CHANGE categoty_id category_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A12469DE2 FOREIGN KEY (category_id) REFERENCES `categories` (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B3BA5A5A12469DE2 ON products (category_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users ADD password VARCHAR(255) NOT NULL, ADD roles JSON NOT NULL, DROP password_hash, DROP role
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE `products` DROP FOREIGN KEY FK_B3BA5A5A12469DE2
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_B3BA5A5A12469DE2 ON `products`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `products` CHANGE category_id categoty_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `products` ADD CONSTRAINT FK_B3BA5A5A9D066842 FOREIGN KEY (categoty_id) REFERENCES categories (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B3BA5A5A9D066842 ON `products` (categoty_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `users` ADD role VARCHAR(255) NOT NULL, DROP roles, CHANGE password password_hash VARCHAR(255) NOT NULL
        SQL);
    }
}
