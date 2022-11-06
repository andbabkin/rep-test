<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221105204817 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE props (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(190) NOT NULL, UNIQUE INDEX UNIQ_5B7EAAA85E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hierarchy (parent_id INT NOT NULL, child_id INT NOT NULL, INDEX IDX_FA7A28AE727ACA70 (parent_id), INDEX IDX_FA7A28AEDD62C21B (child_id), PRIMARY KEY(parent_id, child_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE hierarchy ADD CONSTRAINT FK_FA7A28AE727ACA70 FOREIGN KEY (parent_id) REFERENCES props (id)');
        $this->addSql('ALTER TABLE hierarchy ADD CONSTRAINT FK_FA7A28AEDD62C21B FOREIGN KEY (child_id) REFERENCES props (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hierarchy DROP FOREIGN KEY FK_FA7A28AE727ACA70');
        $this->addSql('ALTER TABLE hierarchy DROP FOREIGN KEY FK_FA7A28AEDD62C21B');
        $this->addSql('DROP TABLE props');
        $this->addSql('DROP TABLE hierarchy');
    }
}
