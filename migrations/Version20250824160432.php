<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250824160432 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agent (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, name VARCHAR(100) NOT NULL, username VARCHAR(100) NOT NULL, descr VARCHAR(150) NOT NULL, system_prompt VARCHAR(1000) NOT NULL, is_public TINYINT(1) NOT NULL, logo VARCHAR(255) DEFAULT NULL, max_tokens SMALLINT UNSIGNED NOT NULL, temperature DOUBLE PRECISION NOT NULL, top_p DOUBLE PRECISION NOT NULL, top_k SMALLINT UNSIGNED NOT NULL, presence_penalty DOUBLE PRECISION NOT NULL, frequency_penalty DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_268B9C9DF85E0677 (username), UNIQUE INDEX UNIQ_268B9C9DE48E9A13 (logo), INDEX IDX_268B9C9DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE agent_access (id INT UNSIGNED AUTO_INCREMENT NOT NULL, agent_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, INDEX IDX_588E7C723414710B (agent_id), INDEX IDX_588E7C72A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT UNSIGNED AUTO_INCREMENT NOT NULL, username VARCHAR(100) NOT NULL, email VARCHAR(120) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, is_verified TINYINT(1) NOT NULL, is_banned TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE agent ADD CONSTRAINT FK_268B9C9DA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE agent_access ADD CONSTRAINT FK_588E7C723414710B FOREIGN KEY (agent_id) REFERENCES agent (id)');
        $this->addSql('ALTER TABLE agent_access ADD CONSTRAINT FK_588E7C72A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agent DROP FOREIGN KEY FK_268B9C9DA76ED395');
        $this->addSql('ALTER TABLE agent_access DROP FOREIGN KEY FK_588E7C723414710B');
        $this->addSql('ALTER TABLE agent_access DROP FOREIGN KEY FK_588E7C72A76ED395');
        $this->addSql('DROP TABLE agent');
        $this->addSql('DROP TABLE agent_access');
        $this->addSql('DROP TABLE `user`');
    }
}
