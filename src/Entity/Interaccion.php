<?php

namespace App\Entity;

use App\Repository\InteraccionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InteraccionRepository::class)]
class Interaccion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Alumno $alumno = null;

    #[ORM\ManyToOne(inversedBy: 'interacciones')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DetallePresentacionActividad $detallePresentacionActividad = null;

    #[ORM\Column]
    private ?bool $correcto = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAlumno(): ?Alumno
    {
        return $this->alumno;
    }

    public function setAlumno(?Alumno $alumno): self
    {
        $this->alumno = $alumno;

        return $this;
    }

    public function getDetallePresentacionActividad(): ?DetallePresentacionActividad
    {
        return $this->detallePresentacionActividad;
    }

    public function setDetallePresentacionActividad(?DetallePresentacionActividad $detallePresentacionActividad): self
    {
        $this->detallePresentacionActividad = $detallePresentacionActividad;

        return $this;
    }

    public function isCorrecto(): ?bool
    {
        return $this->correcto;
    }

    public function setCorrecto(bool $correcto): self
    {
        $this->correcto = $correcto;

        return $this;
    }
}
