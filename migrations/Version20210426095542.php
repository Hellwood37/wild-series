<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210426095542 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE program ADD seasons_id INT NOT NULL');
        $this->addSql('ALTER TABLE program ADD CONSTRAINT FK_92ED778416EB9F66 FOREIGN KEY (seasons_id) REFERENCES season (id)');
        $this->addSql('CREATE INDEX IDX_92ED778416EB9F66 ON program (seasons_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE program DROP FOREIGN KEY FK_92ED778416EB9F66');
        $this->addSql('DROP INDEX IDX_92ED778416EB9F66 ON program');
        $this->addSql('ALTER TABLE program DROP seasons_id');
    }
}
