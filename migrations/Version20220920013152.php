<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220920013152 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE alumno (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, apellido VARCHAR(255) NOT NULL, cua VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE alumno_curso (alumno_id INT NOT NULL, curso_id INT NOT NULL, INDEX IDX_66FE498EFC28E5EE (alumno_id), INDEX IDX_66FE498E87CB4A1F (curso_id), PRIMARY KEY(alumno_id, curso_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE curso (id INT AUTO_INCREMENT NOT NULL, usuario_id INT NOT NULL, grado VARCHAR(50) NOT NULL, division VARCHAR(50) NOT NULL, materia VARCHAR(255) NOT NULL, anio INT NOT NULL, INDEX IDX_CA3B40ECDB38439E (usuario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usuario (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, nombre VARCHAR(255) NOT NULL, apellido VARCHAR(255) NOT NULL, telefono VARCHAR(255) DEFAULT NULL, direccion LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_2265B05DF85E0677 (username), UNIQUE INDEX UNIQ_2265B05DE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE alumno_curso ADD CONSTRAINT FK_66FE498EFC28E5EE FOREIGN KEY (alumno_id) REFERENCES alumno (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE alumno_curso ADD CONSTRAINT FK_66FE498E87CB4A1F FOREIGN KEY (curso_id) REFERENCES curso (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE curso ADD CONSTRAINT FK_CA3B40ECDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alumno_curso DROP FOREIGN KEY FK_66FE498EFC28E5EE');
        $this->addSql('ALTER TABLE alumno_curso DROP FOREIGN KEY FK_66FE498E87CB4A1F');
        $this->addSql('ALTER TABLE curso DROP FOREIGN KEY FK_CA3B40ECDB38439E');
        $this->addSql('DROP TABLE alumno');
        $this->addSql('DROP TABLE alumno_curso');
        $this->addSql('DROP TABLE curso');
        $this->addSql('DROP TABLE usuario');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
