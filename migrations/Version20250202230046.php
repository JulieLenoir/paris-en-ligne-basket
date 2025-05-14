<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250202230046 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE matchs (id INT AUTO_INCREMENT NOT NULL, home_team_name VARCHAR(255) NOT NULL, away_team_name VARCHAR(255) NOT NULL, scheduled_time VARCHAR(255) NOT NULL, home_points INT NOT NULL, away_points INT NOT NULL, game_id VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paris (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, match_id INT DEFAULT NULL, statut VARCHAR(255) NOT NULL, gains INT NOT NULL, perte INT NOT NULL, mise INT NOT NULL, equipe VARCHAR(255) NOT NULL, solde_cloture INT DEFAULT NULL, INDEX IDX_962CCBE2A76ED395 (user_id), INDEX IDX_962CCBE22ABEACD6 (match_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) DEFAULT NULL, prenom VARCHAR(255) DEFAULT NULL, date_naissance DATE DEFAULT NULL, solde INT DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE paris ADD CONSTRAINT FK_962CCBE2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE paris ADD CONSTRAINT FK_962CCBE22ABEACD6 FOREIGN KEY (match_id) REFERENCES matchs (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paris DROP FOREIGN KEY FK_962CCBE2A76ED395');
        $this->addSql('ALTER TABLE paris DROP FOREIGN KEY FK_962CCBE22ABEACD6');
        $this->addSql('DROP TABLE matchs');
        $this->addSql('DROP TABLE paris');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
