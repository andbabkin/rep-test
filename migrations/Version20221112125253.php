<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221112125253 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ancestors (prop_id INT NOT NULL, ancestor_id INT NOT NULL, INDEX IDX_47B6E16ADEB3FFBD (prop_id), INDEX IDX_47B6E16AC671CEA1 (ancestor_id), PRIMARY KEY(prop_id, ancestor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ancestors ADD CONSTRAINT FK_47B6E16ADEB3FFBD FOREIGN KEY (prop_id) REFERENCES props (id)');
        $this->addSql('ALTER TABLE ancestors ADD CONSTRAINT FK_47B6E16AC671CEA1 FOREIGN KEY (ancestor_id) REFERENCES props (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ancestors DROP FOREIGN KEY FK_47B6E16ADEB3FFBD');
        $this->addSql('ALTER TABLE ancestors DROP FOREIGN KEY FK_47B6E16AC671CEA1');
        $this->addSql('DROP TABLE ancestors');
    }
}
