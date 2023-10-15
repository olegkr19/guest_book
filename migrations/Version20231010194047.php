<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231010194047 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message_file DROP FOREIGN KEY FK_250AADC980E261BC');
        $this->addSql('DROP INDEX IDX_250AADC980E261BC ON message_file');
        $this->addSql('ALTER TABLE message_file CHANGE message_id_id message_id INT NOT NULL');
        $this->addSql('ALTER TABLE message_file ADD CONSTRAINT FK_250AADC9537A1329 FOREIGN KEY (message_id) REFERENCES message (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_250AADC9537A1329 ON message_file (message_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message_file DROP FOREIGN KEY FK_250AADC9537A1329');
        $this->addSql('DROP INDEX IDX_250AADC9537A1329 ON message_file');
        $this->addSql('ALTER TABLE message_file CHANGE message_id message_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE message_file ADD CONSTRAINT FK_250AADC980E261BC FOREIGN KEY (message_id_id) REFERENCES message (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_250AADC980E261BC ON message_file (message_id_id)');
    }
}
