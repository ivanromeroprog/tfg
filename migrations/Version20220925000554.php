<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220925000554 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE presentacion_actividad (id INT AUTO_INCREMENT NOT NULL, actividad_id INT NOT NULL, curso_id INT NOT NULL, estado VARCHAR(50) NOT NULL, fecha DATETIME NOT NULL, INDEX IDX_932811546014FACA (actividad_id), INDEX IDX_9328115487CB4A1F (curso_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE presentacion_actividad ADD CONSTRAINT FK_932811546014FACA FOREIGN KEY (actividad_id) REFERENCES actividad (id)');
        $this->addSql('ALTER TABLE presentacion_actividad ADD CONSTRAINT FK_9328115487CB4A1F FOREIGN KEY (curso_id) REFERENCES curso (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE presentacion_actividad DROP FOREIGN KEY FK_932811546014FACA');
        $this->addSql('ALTER TABLE presentacion_actividad DROP FOREIGN KEY FK_9328115487CB4A1F');
        $this->addSql('DROP TABLE presentacion_actividad');
    }
}
