<?php

namespace App\Entity;

use App\Repository\AsistenciaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AsistenciaRepository::class)]
class Asistencia {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'asistencias')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TomaDeAsistencia $tomaDeAsistencia = null;

    #[ORM\ManyToOne(inversedBy: 'asistencias')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Alumno $alumno = null;

    #[ORM\Column]
    private ?bool $presente = null;

    public function __construct(?int $id = null, ?Alumno $alumno = null, ?bool $presente = null, ?TomaDeAsistencia $tomaDeAsistencia = null) {
        $this->id = $id;
        $this->tomaDeAsistencia = $tomaDeAsistencia;
        $this->alumno = $alumno;
        $this->presente = $presente;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getTomaDeAsistencia(): ?TomaDeAsistencia {
        return $this->tomaDeAsistencia;
    }

    public function setTomaDeAsistencia(?TomaDeAsistencia $tomaDeAsistencia): self {
        $this->tomaDeAsistencia = $tomaDeAsistencia;

        return $this;
    }

    public function getAlumno(): ?Alumno {
        return $this->alumno;
    }

    public function setAlumno(?Alumno $alumno): self {
        $this->alumno = $alumno;

        return $this;
    }

    public function isPresente(): ?bool {
        return $this->presente;
    }

    public function setPresente(bool $presente): self {
        $this->presente = $presente;

        return $this;
    }

}
