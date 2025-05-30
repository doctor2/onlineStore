<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250530051048 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_items CHANGE price price INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_items CHANGE price price INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE orders CHANGE total_amount total_amount INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE payments CHANGE amount amount INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE products CHANGE price price INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transactions CHANGE amount amount INT NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE `products` CHANGE price price NUMERIC(10, 2) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `order_items` CHANGE price price NUMERIC(10, 2) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `orders` CHANGE total_amount total_amount NUMERIC(10, 2) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `payments` CHANGE amount amount NUMERIC(10, 2) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `cart_items` CHANGE price price NUMERIC(10, 2) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `transactions` CHANGE amount amount NUMERIC(10, 2) NOT NULL
        SQL);
    }
}
