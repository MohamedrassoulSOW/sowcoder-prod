<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260517231249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blog_comments (id VARCHAR(36) NOT NULL, article_slug VARCHAR(120) NOT NULL, author_name VARCHAR(120) NOT NULL, author_email VARCHAR(200) DEFAULT NULL, user_id VARCHAR(36) DEFAULT NULL, body LONGTEXT NOT NULL, created_at VARCHAR(32) NOT NULL, INDEX idx_blog_comments_slug_created (article_slug, created_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE blog_likes (id VARCHAR(36) NOT NULL, article_slug VARCHAR(120) NOT NULL, liker_key VARCHAR(80) NOT NULL, created_at VARCHAR(32) NOT NULL, UNIQUE INDEX uniq_blog_like (article_slug, liker_key), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE blog_comments');
        $this->addSql('DROP TABLE blog_likes');
    }
}
