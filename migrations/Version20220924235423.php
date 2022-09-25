<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220924235423 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE actividad ADD usuario_id INT NOT NULL');
        $this->addSql('ALTER TABLE actividad ADD CONSTRAINT FK_8DF2BD06DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('CREATE INDEX IDX_8DF2BD06DB38439E ON actividad (usuario_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE actividad DROP FOREIGN KEY FK_8DF2BD06DB38439E');
        $this->addSql('DROP INDEX IDX_8DF2BD06DB38439E ON actividad');
        $this->addSql('ALTER TABLE actividad DROP usuario_id');
    }
}
