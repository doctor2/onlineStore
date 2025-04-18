<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250417175653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE `cart_items` (id INT AUTO_INCREMENT NOT NULL, cart_id INT NOT NULL, product_id INT NOT NULL, quantity INT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_BEF484451AD5CDBF (cart_id), INDEX IDX_BEF484454584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE `categories` (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_3AF34668727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE `order_items` (id INT AUTO_INCREMENT NOT NULL, order_id INT NOT NULL, product_id INT NOT NULL, quantity INT NOT NULL, price NUMERIC(10, 2) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_62809DB08D9F6D38 (order_id), INDEX IDX_62809DB04584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE `orders` (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, total_amount NUMERIC(10, 2) NOT NULL, status VARCHAR(255) NOT NULL, payment_method VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_E52FFDEEA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE `payments` (id INT AUTO_INCREMENT NOT NULL, order_id INT NOT NULL, amount NUMERIC(10, 2) NOT NULL, payment_status VARCHAR(255) NOT NULL, payment_date DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', payment_method VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_65D29B328D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE `products` (id INT AUTO_INCREMENT NOT NULL, categoty_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, price NUMERIC(10, 2) NOT NULL, stock_quantity INT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_B3BA5A5A9D066842 (categoty_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE `shipping_addresses` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, address_line VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, postal_code VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_293F2421A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE `shopping_carts` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_4FA232F6A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE `users` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password_hash VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, role VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `cart_items` ADD CONSTRAINT FK_BEF484451AD5CDBF FOREIGN KEY (cart_id) REFERENCES `shopping_carts` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `cart_items` ADD CONSTRAINT FK_BEF484454584665A FOREIGN KEY (product_id) REFERENCES `products` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `categories` ADD CONSTRAINT FK_3AF34668727ACA70 FOREIGN KEY (parent_id) REFERENCES `categories` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `order_items` ADD CONSTRAINT FK_62809DB08D9F6D38 FOREIGN KEY (order_id) REFERENCES `orders` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `order_items` ADD CONSTRAINT FK_62809DB04584665A FOREIGN KEY (product_id) REFERENCES `products` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `orders` ADD CONSTRAINT FK_E52FFDEEA76ED395 FOREIGN KEY (user_id) REFERENCES `users` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `payments` ADD CONSTRAINT FK_65D29B328D9F6D38 FOREIGN KEY (order_id) REFERENCES `orders` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `products` ADD CONSTRAINT FK_B3BA5A5A9D066842 FOREIGN KEY (categoty_id) REFERENCES `categories` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `shipping_addresses` ADD CONSTRAINT FK_293F2421A76ED395 FOREIGN KEY (user_id) REFERENCES `users` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `shopping_carts` ADD CONSTRAINT FK_4FA232F6A76ED395 FOREIGN KEY (user_id) REFERENCES `users` (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE `cart_items` DROP FOREIGN KEY FK_BEF484451AD5CDBF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `cart_items` DROP FOREIGN KEY FK_BEF484454584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `categories` DROP FOREIGN KEY FK_3AF34668727ACA70
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `order_items` DROP FOREIGN KEY FK_62809DB08D9F6D38
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `order_items` DROP FOREIGN KEY FK_62809DB04584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `orders` DROP FOREIGN KEY FK_E52FFDEEA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `payments` DROP FOREIGN KEY FK_65D29B328D9F6D38
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `products` DROP FOREIGN KEY FK_B3BA5A5A9D066842
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `shipping_addresses` DROP FOREIGN KEY FK_293F2421A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `shopping_carts` DROP FOREIGN KEY FK_4FA232F6A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `cart_items`
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `categories`
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `order_items`
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `orders`
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `payments`
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `products`
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `shipping_addresses`
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `shopping_carts`
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `users`
        SQL);
    }
}
