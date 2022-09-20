<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220920132258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE curso ADD organizacion_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE curso ADD CONSTRAINT FK_CA3B40EC90B1019E FOREIGN KEY (organizacion_id) REFERENCES organizacion (id)');
        $this->addSql('CREATE INDEX IDX_CA3B40EC90B1019E ON curso (organizacion_id)');
        $this->addSql('ALTER TABLE organizacion ADD creador_id INT NOT NULL');
        $this->addSql('ALTER TABLE organizacion ADD CONSTRAINT FK_C200C5A62F40C3D FOREIGN KEY (creador_id) REFERENCES usuario (id)');
        $this->addSql('CREATE INDEX IDX_C200C5A62F40C3D ON organizacion (creador_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE curso DROP FOREIGN KEY FK_CA3B40EC90B1019E');
        $this->addSql('DROP INDEX IDX_CA3B40EC90B1019E ON curso');
        $this->addSql('ALTER TABLE curso DROP organizacion_id');
        $this->addSql('ALTER TABLE organizacion DROP FOREIGN KEY FK_C200C5A62F40C3D');
        $this->addSql('DROP INDEX IDX_C200C5A62F40C3D ON organizacion');
        $this->addSql('ALTER TABLE organizacion DROP creador_id');
    }
}
