<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220925004343 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE detalle_presentacion_actividad (id INT AUTO_INCREMENT NOT NULL, presentacion_actividad_id INT NOT NULL, dato LONGTEXT NOT NULL, tipo VARCHAR(50) NOT NULL, relacion INT DEFAULT NULL, correcto TINYINT(1) DEFAULT NULL, INDEX IDX_3BBFF9656B98C825 (presentacion_actividad_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE detalle_presentacion_actividad ADD CONSTRAINT FK_3BBFF9656B98C825 FOREIGN KEY (presentacion_actividad_id) REFERENCES presentacion_actividad (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE detalle_presentacion_actividad DROP FOREIGN KEY FK_3BBFF9656B98C825');
        $this->addSql('DROP TABLE detalle_presentacion_actividad');
    }
}
