<?php

namespace App\Entity;

use App\Repository\AlumnoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlumnoRepository::class)]
class Alumno
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $apellido = null;

    #[ORM\Column(length: 50)]
    private ?string $cua = null;

    #[ORM\ManyToMany(targetEntity: Curso::class, inversedBy: 'alumnos')]
    private Collection $cursos;

    #[ORM\OneToMany(mappedBy: 'alumno', targetEntity: Asistencia::class)]
    private Collection $asistencias;

    public function __construct(?int $id = null, ?string $nombre = null, ?string $apellido = null, ?string $cua = null) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->cua = $cua;
        $this->cursos = new ArrayCollection();
        $this->asistencias = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApellido(): ?string
    {
        return $this->apellido;
    }

    public function setApellido(string $apellido): self
    {
        $this->apellido = $apellido;

        return $this;
    }

    public function getCua(): ?string
    {
        return $this->cua;
    }

    public function setCua(string $cua): self
    {
        $this->cua = $cua;

        return $this;
    }

    /**
     * @return Collection<int, Curso>
     */
    public function getCursos(): Collection
    {
        return $this->cursos;
    }

    public function addCurso(Curso $curso): self
    {
        if (!$this->cursos->contains($curso)) {
            $this->cursos->add($curso);
        }

        return $this;
    }
    
    public function hasCurso(Curso $curso): bool{
        return $this->cursos->contains($curso);
    }

    public function removeCurso(Curso $curso): self
    {
        $this->cursos->removeElement($curso);

        return $this;
    }
    
    public function __toString() {
        return $this->apellido . ', ' . $this->nombre . ' ('. $this->cua .')';
    }

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
            $asistencia->setAlumno($this);
        }

        return $this;
    }

    public function removeAsistencia(Asistencia $asistencia): self
    {
        if ($this->asistencias->removeElement($asistencia)) {
            // set the owning side to null (unless already changed)
            if ($asistencia->getAlumno() === $this) {
                $asistencia->setAlumno(null);
            }
        }

        return $this;
    }
}
