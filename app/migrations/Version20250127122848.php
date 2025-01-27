<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250127122848 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event CHANGE start_date start_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', CHANGE start_time start_time TIME NOT NULL COMMENT \'(DC2Type:time_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event CHANGE start_date start_date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', CHANGE start_time start_time TIME DEFAULT NULL COMMENT \'(DC2Type:time_immutable)\'');
    }
}
