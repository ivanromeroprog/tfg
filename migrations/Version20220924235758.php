<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220924235758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE detalle_actividad ADD actividad_id INT NOT NULL');
        $this->addSql('ALTER TABLE detalle_actividad ADD CONSTRAINT FK_AC9E0C466014FACA FOREIGN KEY (actividad_id) REFERENCES actividad (id)');
        $this->addSql('CREATE INDEX IDX_AC9E0C466014FACA ON detalle_actividad (actividad_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE detalle_actividad DROP FOREIGN KEY FK_AC9E0C466014FACA');
        $this->addSql('DROP INDEX IDX_AC9E0C466014FACA ON detalle_actividad');
        $this->addSql('ALTER TABLE detalle_actividad DROP actividad_id');
    }
}
