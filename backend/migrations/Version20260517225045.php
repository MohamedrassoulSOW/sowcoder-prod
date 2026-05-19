<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260517225045 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contacts (id VARCHAR(36) NOT NULL, name VARCHAR(120) NOT NULL, email VARCHAR(200) NOT NULL, phone VARCHAR(30) DEFAULT NULL, subject VARCHAR(200) DEFAULT NULL, message LONGTEXT NOT NULL, created_at VARCHAR(32) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE inscriptions (id VARCHAR(36) NOT NULL, name VARCHAR(120) NOT NULL, email VARCHAR(200) NOT NULL, phone VARCHAR(30) DEFAULT NULL, formation_title VARCHAR(200) NOT NULL, message LONGTEXT DEFAULT NULL, created_at VARCHAR(32) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE orders (id VARCHAR(36) NOT NULL, type VARCHAR(20) NOT NULL, name VARCHAR(120) NOT NULL, email VARCHAR(200) NOT NULL, phone VARCHAR(30) DEFAULT NULL, product_title VARCHAR(200) NOT NULL, message LONGTEXT DEFAULT NULL, created_at VARCHAR(32) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE users (id VARCHAR(36) NOT NULL, name VARCHAR(120) NOT NULL, email VARCHAR(200) NOT NULL, password_hash VARCHAR(255) NOT NULL, role VARCHAR(10) NOT NULL, created_at VARCHAR(32) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE contacts');
        $this->addSql('DROP TABLE inscriptions');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE users');
    }
}
