<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220924230455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE toma_de_asistencia (id INT AUTO_INCREMENT NOT NULL, curso_id INT NOT NULL, fecha DATETIME NOT NULL, estado VARCHAR(50) NOT NULL, url VARCHAR(255) NOT NULL, INDEX IDX_4BC66AF87CB4A1F (curso_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE toma_de_asistencia ADD CONSTRAINT FK_4BC66AF87CB4A1F FOREIGN KEY (curso_id) REFERENCES curso (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE toma_de_asistencia DROP FOREIGN KEY FK_4BC66AF87CB4A1F');
        $this->addSql('DROP TABLE toma_de_asistencia');
    }
}
