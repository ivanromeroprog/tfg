<?php

namespace App\Entity;

use App\Repository\DetalleActividadRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetalleActividadRepository::class)]
class DetalleActividad
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $dato = null;

    #[ORM\Column(length: 50)]
    private ?string $tipo = null;

    #[ORM\Column(nullable: true)]
    private ?int $relacion = null;

    #[ORM\Column(nullable: true)]
    private ?bool $correcto = null;

    #[ORM\ManyToOne(inversedBy: 'detallesactividad')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Actividad $actividad = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDato(): ?string
    {
        return $this->dato;
    }

    public function setDato(string $dato): self
    {
        $this->dato = $dato;

        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getRelacion(): ?int
    {
        return $this->relacion;
    }

    public function setRelacion(?int $relacion): self
    {
        $this->relacion = $relacion;

        return $this;
    }

    public function isCorrecto(): ?bool
    {
        return $this->correcto;
    }

    public function setCorrecto(?bool $correcto): self
    {
        $this->correcto = $correcto;

        return $this;
    }

    public function getActividad(): ?Actividad
    {
        return $this->actividad;
    }

    public function setActividad(?Actividad $actividad): self
    {
        $this->actividad = $actividad;

        return $this;
    }
}
