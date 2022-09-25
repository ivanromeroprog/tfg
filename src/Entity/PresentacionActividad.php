<?php

namespace App\Entity;

use App\Repository\PresentacionActividadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PresentacionActividadRepository::class)]
class PresentacionActividad
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Curso $curso = null;

    #[ORM\Column(length: 50)]
    private ?string $estado = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuario = null;

    #[ORM\Column(length: 255)]
    private ?string $titulo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\OneToMany(mappedBy: 'presentacionActividad', targetEntity: DetallePresentacionActividad::class, orphanRemoval: true)]
    private Collection $detallesPresentacionActividad;

    public function __construct()
    {
        $this->detallesPresentacionActividad = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurso(): ?Curso
    {
        return $this->curso;
    }

    public function setCurso(?Curso $curso): self
    {
        $this->curso = $curso;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * @return Collection<int, DetallePresentacionActividad>
     */
    public function getDetallesPresentacionActividad(): Collection
    {
        return $this->detallesPresentacionActividad;
    }

    public function addDetallesPresentacionActividad(DetallePresentacionActividad $detallesPresentacionActividad): self
    {
        if (!$this->detallesPresentacionActividad->contains($detallesPresentacionActividad)) {
            $this->detallesPresentacionActividad->add($detallesPresentacionActividad);
            $detallesPresentacionActividad->setPresentacionActividad($this);
        }

        return $this;
    }

    public function removeDetallesPresentacionActividad(DetallePresentacionActividad $detallesPresentacionActividad): self
    {
        if ($this->detallesPresentacionActividad->removeElement($detallesPresentacionActividad)) {
            // set the owning side to null (unless already changed)
            if ($detallesPresentacionActividad->getPresentacionActividad() === $this) {
                $detallesPresentacionActividad->setPresentacionActividad(null);
            }
        }

        return $this;
    }
}
