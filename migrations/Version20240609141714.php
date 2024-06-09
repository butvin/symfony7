<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240609141714 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'init migration ';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE index (uuid UUID NOT NULL, hash TEXT DEFAULT NULL, name VARCHAR(255) NOT NULL, active BOOLEAN DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(uuid))');
        $this->addSql('COMMENT ON COLUMN index.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN index.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN index.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN index.deleted_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE index');
    }
}
