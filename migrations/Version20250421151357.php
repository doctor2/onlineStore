<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250421151357 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_items ADD price NUMERIC(10, 2) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE orders ADD shipping_address_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE4D4CFF2B FOREIGN KEY (shipping_address_id) REFERENCES `shipping_addresses` (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E52FFDEE4D4CFF2B ON orders (shipping_address_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shipping_addresses DROP state, DROP country
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shopping_carts DROP INDEX IDX_4FA232F6A76ED395, ADD UNIQUE INDEX UNIQ_4FA232F6A76ED395 (user_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE `cart_items` DROP price
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `shipping_addresses` ADD state VARCHAR(255) NOT NULL, ADD country VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `orders` DROP FOREIGN KEY FK_E52FFDEE4D4CFF2B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_E52FFDEE4D4CFF2B ON `orders`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `orders` DROP shipping_address_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `shopping_carts` DROP INDEX UNIQ_4FA232F6A76ED395, ADD INDEX IDX_4FA232F6A76ED395 (user_id)
        SQL);
    }
}
