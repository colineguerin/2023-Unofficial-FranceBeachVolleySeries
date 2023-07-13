<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230712101733 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE result (id INT AUTO_INCREMENT NOT NULL, tournament_id INT DEFAULT NULL, team_id INT DEFAULT NULL, ranking INT NOT NULL, points INT NOT NULL, INDEX IDX_136AC11333D1A3E7 (tournament_id), INDEX IDX_136AC113296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE result ADD CONSTRAINT FK_136AC11333D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id)');
        $this->addSql('ALTER TABLE result ADD CONSTRAINT FK_136AC113296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE result DROP FOREIGN KEY FK_136AC11333D1A3E7');
        $this->addSql('ALTER TABLE result DROP FOREIGN KEY FK_136AC113296CD8AE');
        $this->addSql('DROP TABLE result');
    }
}
