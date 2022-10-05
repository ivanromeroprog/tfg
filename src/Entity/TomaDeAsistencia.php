<?php

namespace App\Entity;

use App\Repository\TomaDeAsistenciaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TomaDeAsistenciaRepository::class)]
class TomaDeAsistencia
{
    const ESTADO_INICIADO = 'Iniciado';
    const ESTADO_FINALIZADO = 'Finalizado';
    const ESTADOS = [
        self::ESTADO_INICIADO => self::ESTADO_INICIADO,
        self::ESTADO_FINALIZADO => self::ESTADO_FINALIZADO
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'tomasDeAsistencia')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Curso $curso = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(length: 50)]
    private ?string $estado = null;
    /*
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;
*/
    #[ORM\OneToMany(mappedBy: 'tomaDeAsistencia', targetEntity: Asistencia::class)]
    private Collection $asistencias;

    public function __construct()
    {
        $this->asistencias = new ArrayCollection();
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

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(?\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): self
    {
        if (!in_array($estado, array(self::ESTADO_FINALIZADO, self::ESTADO_INICIADO))) {
            throw new \InvalidArgumentException("Estado inválido");
        }
        $this->estado = $estado;

        return $this;
    }
    /*
    public function getUrl(): ?string
    {
        return $this->id;
    }
    */
    public function getUrlEncoded(): ?string
    {
        return $this->base64_url_encode($this->id);
    }

    static public function urlDecode(string $encoded_url): ?string
    {
        return self::base64_url_decode($encoded_url);
    }
    /*
    public function setUrl(?string $url): void {
        $this->url = $url;
    }

    public function setUrlEncoded(string $url): self
    {
        $this->url = base64_url_decode($url);

        return $this;
    }
    */
    /**
     * @return Collection<int, Asistencia>
     */
    public function getAsistencias(): Collection
    {
        return $this->asistencias;
    }

    public function addAsistencia(Asistencia $asistencia): self
    {
        if (!$this->asistencias->contains($asistencia)) {
            $this->asistencias->add($asistencia);
            $asistencia->setTomaDeAsistencia($this);
        }

        return $this;
    }

    public function removeAsistencia(Asistencia $asistencia): self
    {
        if ($this->asistencias->removeElement($asistencia)) {
            // set the owning side to null (unless already changed)
            if ($asistencia->getTomaDeAsistencia() === $this) {
                $asistencia->setTomaDeAsistencia(null);
            }
        }

        return $this;
    }

    private function base64_url_encode($input)
    {
        return strtr(base64_encode($input), '+/=', '._-');
    }

    static private function base64_url_decode($input)
    {
        return base64_decode(strtr($input, '._-', '+/='));
    }
}
