<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250512142657 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE `transactions` (id INT AUTO_INCREMENT NOT NULL, payment_id INT NOT NULL, amount NUMERIC(10, 2) NOT NULL, status VARCHAR(50) NOT NULL, payment_method VARCHAR(50) NOT NULL, transaction_id VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_EAA81A4C4C3A3BB (payment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `transactions` ADD CONSTRAINT FK_EAA81A4C4C3A3BB FOREIGN KEY (payment_id) REFERENCES `payments` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE orders CHANGE payment_method payment_method VARCHAR(50) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE payments ADD status VARCHAR(50) NOT NULL, ADD updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', DROP payment_status, CHANGE payment_method payment_method VARCHAR(50) NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE `transactions` DROP FOREIGN KEY FK_EAA81A4C4C3A3BB
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `transactions`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `orders` CHANGE payment_method payment_method VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `payments` ADD payment_status VARCHAR(255) NOT NULL, DROP status, DROP updated_at, CHANGE payment_method payment_method VARCHAR(255) NOT NULL
        SQL);
    }
}
