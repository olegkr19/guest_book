<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231007212835 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE message_file (id INT AUTO_INCREMENT NOT NULL, message_id INT NOT NULL, filename VARCHAR(255) NOT NULL, INDEX IDX_250AADC980E261BC (message_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE message_file ADD CONSTRAINT FK_250AADC980E261BC FOREIGN KEY (message_id) REFERENCES message (id) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message_file DROP FOREIGN KEY FK_250AADC980E261BC');
        $this->addSql('DROP TABLE message_file');
    }
}
