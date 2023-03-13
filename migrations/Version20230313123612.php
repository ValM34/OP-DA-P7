<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230313123612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer CHANGE name name VARCHAR(150) NOT NULL, CHANGE surname surname VARCHAR(150) NOT NULL, CHANGE email email VARCHAR(180) NOT NULL');
        $this->addSql('ALTER TABLE product CHANGE description description VARCHAR(1500) NOT NULL');
        $this->addSql('ALTER TABLE vendor CHANGE name name VARCHAR(150) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer CHANGE name name VARCHAR(255) NOT NULL, CHANGE surname surname VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE product CHANGE description description VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE vendor CHANGE name name VARCHAR(255) NOT NULL');
    }
}
