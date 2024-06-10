<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240610114036 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'init migration';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "INDEX_HASH" (uuid UUID NOT NULL, hash TEXT NOT NULL, name VARCHAR(255) NOT NULL, active BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE INDEX idx_i_created_at ON "INDEX_HASH" (created_at)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_HASH ON "INDEX_HASH" (hash)');
        $this->addSql('COMMENT ON COLUMN "INDEX_HASH".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "INDEX_HASH".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "INDEX_HASH".updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "INDEX_HASH".deleted_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE "INDEX_HASH"');
    }
}
