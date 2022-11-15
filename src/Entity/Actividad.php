<?php

namespace App\Entity;

use App\Repository\ActividadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActividadRepository::class)]
class Actividad
{

    const TIPO_CUESTIONARIO = 'Cuestionario';
    const TIPO_NUBE_DE_PALABRAS = 'Nube de Palabras';
    const TIPO_RELACIONAR_CONCEPTOS = 'Relacionar Conceptos';
    const TIPO_COMPLETAR_TEXTO = 'Completar Texto';
    const TIPOS = [
        self::TIPO_CUESTIONARIO => self::TIPO_CUESTIONARIO,
        self::TIPO_NUBE_DE_PALABRAS => self::TIPO_NUBE_DE_PALABRAS,
        self::TIPO_RELACIONAR_CONCEPTOS => self::TIPO_RELACIONAR_CONCEPTOS,
        self::TIPO_COMPLETAR_TEXTO => self::TIPO_COMPLETAR_TEXTO,
    ];
    /*
    const ESTADO_INICIADO = 'Iniciado';
    const ESTADO_FINALIZADO = 'Finalizado';
    const ESTADO_ANULADO = 'Anulado';
    const ESTADOS = [
        self::ESTADO_INICIADO => self::ESTADO_INICIADO,
        self::ESTADO_FINALIZADO => self::ESTADO_FINALIZADO,
        self::ESTADO_ANULADO => self::ESTADO_ANULADO,
    ];
    */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titulo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(length: 50)]
    private ?string $tipo = null;
    /*
    #[ORM\Column(length: 50)]
    private ?string $estado = null;
*/
    #[ORM\ManyToOne(inversedBy: 'actividades')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuario = null;

    #[ORM\OneToMany(mappedBy: 'actividad', targetEntity: DetalleActividad::class, orphanRemoval: true)]
    #[ORM\OrderBy(['relacion' => 'ASC', 'id' => 'ASC'])]
    private Collection $detallesactividad;

    public function __construct()
    {
        $this->detallesactividad = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        if (!in_array($tipo, self::TIPOS)) {
            throw new \InvalidArgumentException("Estado inválido");
        }
        $this->tipo = $tipo;

        return $this;
    }
    /*
    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }
*/
    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * @return Collection<int, DetalleActividad>
     */
    public function getDetallesactividad(): Collection
    {
        return $this->detallesactividad;
    }

    public function addDetallesactividad(DetalleActividad $detallesactividad): self
    {
        if (!$this->detallesactividad->contains($detallesactividad)) {
            $this->detallesactividad->add($detallesactividad);
            $detallesactividad->setActividad($this);
        }

        return $this;
    }

    public function removeDetallesactividad(DetalleActividad $detallesactividad): self
    {
        if ($this->detallesactividad->removeElement($detallesactividad)) {
            // set the owning side to null (unless already changed)
            if ($detallesactividad->getActividad() === $this) {
                $detallesactividad->setActividad(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getTitulo() . ' (' . $this->getTipo() . ')';
    }
}
