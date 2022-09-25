<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220925005521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE interaccion (id INT AUTO_INCREMENT NOT NULL, alumno_id INT NOT NULL, detalle_presentacion_actividad_id INT NOT NULL, INDEX IDX_FA439281FC28E5EE (alumno_id), INDEX IDX_FA439281B6FD3829 (detalle_presentacion_actividad_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE interaccion ADD CONSTRAINT FK_FA439281FC28E5EE FOREIGN KEY (alumno_id) REFERENCES alumno (id)');
        $this->addSql('ALTER TABLE interaccion ADD CONSTRAINT FK_FA439281B6FD3829 FOREIGN KEY (detalle_presentacion_actividad_id) REFERENCES detalle_presentacion_actividad (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE interaccion DROP FOREIGN KEY FK_FA439281FC28E5EE');
        $this->addSql('ALTER TABLE interaccion DROP FOREIGN KEY FK_FA439281B6FD3829');
        $this->addSql('DROP TABLE interaccion');
    }
}
