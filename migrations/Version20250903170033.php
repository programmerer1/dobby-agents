<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250903170033 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE message_point_history (id INT AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, message_id INT UNSIGNED NOT NULL, points INT UNSIGNED DEFAULT 0 NOT NULL, INDEX IDX_D9C3CFFFA76ED395 (user_id), UNIQUE INDEX UNIQ_D9C3CFFF537A1329 (message_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE message_point_history ADD CONSTRAINT FK_D9C3CFFFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message_point_history ADD CONSTRAINT FK_D9C3CFFF537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE user ADD points INT UNSIGNED DEFAULT 0 NOT NULL, CHANGE login_failed_attempts login_failed_attempts SMALLINT UNSIGNED DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message_point_history DROP FOREIGN KEY FK_D9C3CFFFA76ED395');
        $this->addSql('ALTER TABLE message_point_history DROP FOREIGN KEY FK_D9C3CFFF537A1329');
        $this->addSql('DROP TABLE message_point_history');
        $this->addSql('ALTER TABLE user DROP points, CHANGE login_failed_attempts login_failed_attempts SMALLINT DEFAULT 0 NOT NULL');
    }
}
