<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231121113405 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE about_us CHANGE quote quote VARCHAR(1000) DEFAULT NULL, CHANGE description description VARCHAR(60000) NOT NULL, CHANGE thumbnail thumbnail VARCHAR(1000) DEFAULT NULL, CHANGE adress adress VARCHAR(1000) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE about_us CHANGE quote quote VARCHAR(255) DEFAULT NULL, CHANGE description description VARCHAR(255) NOT NULL, CHANGE thumbnail thumbnail VARCHAR(255) DEFAULT NULL, CHANGE adress adress VARCHAR(255) NOT NULL');
    }
}
