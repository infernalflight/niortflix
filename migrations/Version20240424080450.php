<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240424080450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE serie_category (serie_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_4ECAB6F9D94388BD (serie_id), INDEX IDX_4ECAB6F912469DE2 (category_id), PRIMARY KEY(serie_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE serie_category ADD CONSTRAINT FK_4ECAB6F9D94388BD FOREIGN KEY (serie_id) REFERENCES serie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE serie_category ADD CONSTRAINT FK_4ECAB6F912469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE serie_category DROP FOREIGN KEY FK_4ECAB6F9D94388BD');
        $this->addSql('ALTER TABLE serie_category DROP FOREIGN KEY FK_4ECAB6F912469DE2');
        $this->addSql('DROP TABLE serie_category');
    }
}
