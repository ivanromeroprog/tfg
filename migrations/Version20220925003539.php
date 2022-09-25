<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220925003539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE presentacion_actividad DROP FOREIGN KEY FK_932811546014FACA');
        $this->addSql('DROP INDEX IDX_932811546014FACA ON presentacion_actividad');
        $this->addSql('ALTER TABLE presentacion_actividad ADD titulo VARCHAR(255) NOT NULL, ADD descripcion LONGTEXT DEFAULT NULL, CHANGE actividad_id usuario_id INT NOT NULL');
        $this->addSql('ALTER TABLE presentacion_actividad ADD CONSTRAINT FK_93281154DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('CREATE INDEX IDX_93281154DB38439E ON presentacion_actividad (usuario_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE presentacion_actividad DROP FOREIGN KEY FK_93281154DB38439E');
        $this->addSql('DROP INDEX IDX_93281154DB38439E ON presentacion_actividad');
        $this->addSql('ALTER TABLE presentacion_actividad DROP titulo, DROP descripcion, CHANGE usuario_id actividad_id INT NOT NULL');
        $this->addSql('ALTER TABLE presentacion_actividad ADD CONSTRAINT FK_932811546014FACA FOREIGN KEY (actividad_id) REFERENCES actividad (id)');
        $this->addSql('CREATE INDEX IDX_932811546014FACA ON presentacion_actividad (actividad_id)');
    }
}
