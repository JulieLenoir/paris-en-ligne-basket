<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250128102947 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paris ADD macth_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE paris ADD CONSTRAINT FK_962CCBE2E6ADD567 FOREIGN KEY (macth_id) REFERENCES matchs (id)');
        $this->addSql('CREATE INDEX IDX_962CCBE2E6ADD567 ON paris (macth_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paris DROP FOREIGN KEY FK_962CCBE2E6ADD567');
        $this->addSql('DROP INDEX IDX_962CCBE2E6ADD567 ON paris');
        $this->addSql('ALTER TABLE paris DROP macth_id');
    }
}
