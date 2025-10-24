<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251018171121 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE alumno (id INT AUTO_INCREMENT NOT NULL, carrera_id INT DEFAULT NULL, nombres VARCHAR(255) NOT NULL, apellidos VARCHAR(255) NOT NULL, fecha_nacimiento DATE NOT NULL, fotografia VARCHAR(500) DEFAULT NULL, INDEX IDX_1435D52DC671B40F (carrera_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE carrera (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, descripcion LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE curso (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, descripcion LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE curso_carrera_semestre (id INT AUTO_INCREMENT NOT NULL, curso_id INT NOT NULL, carrera_id INT NOT NULL, semestre_id INT NOT NULL, INDEX IDX_9E05A14487CB4A1F (curso_id), INDEX IDX_9E05A144C671B40F (carrera_id), INDEX IDX_9E05A1445577AFDB (semestre_id), UNIQUE INDEX unique_curso_carrera_semestre (curso_id, carrera_id, semestre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE curso_seccion (id INT AUTO_INCREMENT NOT NULL, curso_carrera_semestre_id INT NOT NULL, seccion_id INT NOT NULL, INDEX IDX_C999B6F7D6A49995 (curso_carrera_semestre_id), INDEX IDX_C999B6F77A5A413A (seccion_id), UNIQUE INDEX unique_curso_seccion (curso_carrera_semestre_id, seccion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inscripcion (id INT AUTO_INCREMENT NOT NULL, alumno_id INT NOT NULL, curso_seccion_id INT NOT NULL, curso_carrera_semestre_id INT NOT NULL, fecha_inscripcion DATETIME NOT NULL, activo TINYINT(1) NOT NULL, INDEX IDX_935E99F0FC28E5EE (alumno_id), INDEX IDX_935E99F0ED868272 (curso_seccion_id), INDEX IDX_935E99F0D6A49995 (curso_carrera_semestre_id), UNIQUE INDEX unique_alumno_curso_seccion (alumno_id, curso_seccion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nota (id INT AUTO_INCREMENT NOT NULL, alumno_id INT NOT NULL, curso_id INT NOT NULL, seccion_id INT NOT NULL, semestre_id INT NOT NULL, calificacion NUMERIC(5, 2) NOT NULL, fecha_registro DATETIME NOT NULL, aprobado TINYINT(1) NOT NULL, INDEX IDX_C8D03E0DFC28E5EE (alumno_id), INDEX IDX_C8D03E0D87CB4A1F (curso_id), INDEX IDX_C8D03E0D7A5A413A (seccion_id), INDEX IDX_C8D03E0D5577AFDB (semestre_id), UNIQUE INDEX unique_alumno_curso_semestre (alumno_id, curso_id, semestre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE seccion (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(10) NOT NULL, descripcion VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE semestre (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(50) NOT NULL, fecha_inicio DATE NOT NULL, fecha_fin DATE NOT NULL, activo TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE alumno ADD CONSTRAINT FK_1435D52DC671B40F FOREIGN KEY (carrera_id) REFERENCES carrera (id)');
        $this->addSql('ALTER TABLE curso_carrera_semestre ADD CONSTRAINT FK_9E05A14487CB4A1F FOREIGN KEY (curso_id) REFERENCES curso (id)');
        $this->addSql('ALTER TABLE curso_carrera_semestre ADD CONSTRAINT FK_9E05A144C671B40F FOREIGN KEY (carrera_id) REFERENCES carrera (id)');
        $this->addSql('ALTER TABLE curso_carrera_semestre ADD CONSTRAINT FK_9E05A1445577AFDB FOREIGN KEY (semestre_id) REFERENCES semestre (id)');
        $this->addSql('ALTER TABLE curso_seccion ADD CONSTRAINT FK_C999B6F7D6A49995 FOREIGN KEY (curso_carrera_semestre_id) REFERENCES curso_carrera_semestre (id)');
        $this->addSql('ALTER TABLE curso_seccion ADD CONSTRAINT FK_C999B6F77A5A413A FOREIGN KEY (seccion_id) REFERENCES seccion (id)');
        $this->addSql('ALTER TABLE inscripcion ADD CONSTRAINT FK_935E99F0FC28E5EE FOREIGN KEY (alumno_id) REFERENCES alumno (id)');
        $this->addSql('ALTER TABLE inscripcion ADD CONSTRAINT FK_935E99F0ED868272 FOREIGN KEY (curso_seccion_id) REFERENCES curso_seccion (id)');
        $this->addSql('ALTER TABLE inscripcion ADD CONSTRAINT FK_935E99F0D6A49995 FOREIGN KEY (curso_carrera_semestre_id) REFERENCES curso_carrera_semestre (id)');
        $this->addSql('ALTER TABLE nota ADD CONSTRAINT FK_C8D03E0DFC28E5EE FOREIGN KEY (alumno_id) REFERENCES alumno (id)');
        $this->addSql('ALTER TABLE nota ADD CONSTRAINT FK_C8D03E0D87CB4A1F FOREIGN KEY (curso_id) REFERENCES curso (id)');
        $this->addSql('ALTER TABLE nota ADD CONSTRAINT FK_C8D03E0D7A5A413A FOREIGN KEY (seccion_id) REFERENCES seccion (id)');
        $this->addSql('ALTER TABLE nota ADD CONSTRAINT FK_C8D03E0D5577AFDB FOREIGN KEY (semestre_id) REFERENCES semestre (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alumno DROP FOREIGN KEY FK_1435D52DC671B40F');
        $this->addSql('ALTER TABLE curso_carrera_semestre DROP FOREIGN KEY FK_9E05A14487CB4A1F');
        $this->addSql('ALTER TABLE curso_carrera_semestre DROP FOREIGN KEY FK_9E05A144C671B40F');
        $this->addSql('ALTER TABLE curso_carrera_semestre DROP FOREIGN KEY FK_9E05A1445577AFDB');
        $this->addSql('ALTER TABLE curso_seccion DROP FOREIGN KEY FK_C999B6F7D6A49995');
        $this->addSql('ALTER TABLE curso_seccion DROP FOREIGN KEY FK_C999B6F77A5A413A');
        $this->addSql('ALTER TABLE inscripcion DROP FOREIGN KEY FK_935E99F0FC28E5EE');
        $this->addSql('ALTER TABLE inscripcion DROP FOREIGN KEY FK_935E99F0ED868272');
        $this->addSql('ALTER TABLE inscripcion DROP FOREIGN KEY FK_935E99F0D6A49995');
        $this->addSql('ALTER TABLE nota DROP FOREIGN KEY FK_C8D03E0DFC28E5EE');
        $this->addSql('ALTER TABLE nota DROP FOREIGN KEY FK_C8D03E0D87CB4A1F');
        $this->addSql('ALTER TABLE nota DROP FOREIGN KEY FK_C8D03E0D7A5A413A');
        $this->addSql('ALTER TABLE nota DROP FOREIGN KEY FK_C8D03E0D5577AFDB');
        $this->addSql('DROP TABLE alumno');
        $this->addSql('DROP TABLE carrera');
        $this->addSql('DROP TABLE curso');
        $this->addSql('DROP TABLE curso_carrera_semestre');
        $this->addSql('DROP TABLE curso_seccion');
        $this->addSql('DROP TABLE inscripcion');
        $this->addSql('DROP TABLE nota');
        $this->addSql('DROP TABLE seccion');
        $this->addSql('DROP TABLE semestre');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
