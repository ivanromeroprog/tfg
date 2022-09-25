<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220924232640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE asistencia (id INT AUTO_INCREMENT NOT NULL, toma_de_asistencia_id INT NOT NULL, alumno_id INT NOT NULL, presente TINYINT(1) NOT NULL, INDEX IDX_D8264A8D4515ECEF (toma_de_asistencia_id), INDEX IDX_D8264A8DFC28E5EE (alumno_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE asistencia ADD CONSTRAINT FK_D8264A8D4515ECEF FOREIGN KEY (toma_de_asistencia_id) REFERENCES toma_de_asistencia (id)');
        $this->addSql('ALTER TABLE asistencia ADD CONSTRAINT FK_D8264A8DFC28E5EE FOREIGN KEY (alumno_id) REFERENCES alumno (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asistencia DROP FOREIGN KEY FK_D8264A8D4515ECEF');
        $this->addSql('ALTER TABLE asistencia DROP FOREIGN KEY FK_D8264A8DFC28E5EE');
        $this->addSql('DROP TABLE asistencia');
    }
}
