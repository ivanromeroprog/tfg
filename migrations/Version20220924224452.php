<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220924224452 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invitacion (id INT AUTO_INCREMENT NOT NULL, organizacion_id INT NOT NULL, usuario_origen_id INT NOT NULL, usuario_destino_id INT NOT NULL, rol VARCHAR(255) NOT NULL, INDEX IDX_3CD30E8490B1019E (organizacion_id), INDEX IDX_3CD30E841A6974DF (usuario_origen_id), INDEX IDX_3CD30E8417064CB7 (usuario_destino_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invitacion ADD CONSTRAINT FK_3CD30E8490B1019E FOREIGN KEY (organizacion_id) REFERENCES organizacion (id)');
        $this->addSql('ALTER TABLE invitacion ADD CONSTRAINT FK_3CD30E841A6974DF FOREIGN KEY (usuario_origen_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE invitacion ADD CONSTRAINT FK_3CD30E8417064CB7 FOREIGN KEY (usuario_destino_id) REFERENCES usuario (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invitacion DROP FOREIGN KEY FK_3CD30E8490B1019E');
        $this->addSql('ALTER TABLE invitacion DROP FOREIGN KEY FK_3CD30E841A6974DF');
        $this->addSql('ALTER TABLE invitacion DROP FOREIGN KEY FK_3CD30E8417064CB7');
        $this->addSql('DROP TABLE invitacion');
    }
}
