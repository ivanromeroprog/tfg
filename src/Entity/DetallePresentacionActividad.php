<?php

namespace App\Entity;

use App\Repository\DetallePresentacionActividadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetallePresentacionActividadRepository::class)]
class DetallePresentacionActividad {

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

    #[ORM\ManyToOne(inversedBy: 'detallesPresentacionActividad')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PresentacionActividad $presentacionActividad = null;

    #[ORM\OneToMany(mappedBy: 'detallePresentacionActividad', targetEntity: Interaccion::class)]
    private Collection $interacciones;

    public function __construct(
            ?int $id = null,
            ?string $dato = null,
            ?string $tipo = null,
            ?int $relacion = null,
            ?bool $correcto = null,
            ?PresentacionActividad $presentacionActividad = null
    ) {
        $this->id = $id;
        $this->dato = $dato;
        $this->tipo = $tipo;
        $this->relacion = $relacion;
        $this->correcto = $correcto;
        $this->presentacionActividad = $presentacionActividad;
        $this->interacciones = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getDato(): ?string {
        return $this->dato;
    }

    public function setDato(string $dato): self {
        $this->dato = $dato;

        return $this;
    }

    public function getTipo(): ?string {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self {
        $this->tipo = $tipo;

        return $this;
    }

    public function getRelacion(): ?int {
        return $this->relacion;
    }

    public function setRelacion(?int $relacion): self {
        $this->relacion = $relacion;

        return $this;
    }

    public function isCorrecto(): ?bool {
        return $this->correcto;
    }

    public function setCorrecto(?bool $correcto): self {
        $this->correcto = $correcto;

        return $this;
    }

    public function getPresentacionActividad(): ?PresentacionActividad {
        return $this->presentacionActividad;
    }

    public function setPresentacionActividad(?PresentacionActividad $presentacionActividad): self {
        $this->presentacionActividad = $presentacionActividad;

        return $this;
    }

    /**
     * @return Collection<int, Interaccion>
     */
    public function getInteracciones(): Collection {
        return $this->interacciones;
    }

    public function addInteraccion(Interaccion $interaccione): self {
        if (!$this->interacciones->contains($interaccione)) {
            $this->interacciones->add($interaccione);
            $interaccione->setDetallePresentacionActividad($this);
        }

        return $this;
    }

    public function removeInteraccione(Interaccion $interaccione): self {
        if ($this->interacciones->removeElement($interaccione)) {
            // set the owning side to null (unless already changed)
            if ($interaccione->getDetallePresentacionActividad() === $this) {
                $interaccione->setDetallePresentacionActividad(null);
            }
        }

        return $this;
    }

}
