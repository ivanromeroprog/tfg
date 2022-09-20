<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220920135603 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE organizacion_usuario (organizacion_id INT NOT NULL, usuario_id INT NOT NULL, INDEX IDX_97373C6D90B1019E (organizacion_id), INDEX IDX_97373C6DDB38439E (usuario_id), PRIMARY KEY(organizacion_id, usuario_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE organizacion_usuario ADD CONSTRAINT FK_97373C6D90B1019E FOREIGN KEY (organizacion_id) REFERENCES organizacion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE organizacion_usuario ADD CONSTRAINT FK_97373C6DDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE organizacion_usuario DROP FOREIGN KEY FK_97373C6D90B1019E');
        $this->addSql('ALTER TABLE organizacion_usuario DROP FOREIGN KEY FK_97373C6DDB38439E');
        $this->addSql('DROP TABLE organizacion_usuario');
    }
}
