<?php

namespace App\Entity;

use App\Repository\CursoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CursoRepository::class)]
class Curso
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $grado = null;

    #[ORM\Column(length: 50)]
    private ?string $division = null;

    #[ORM\Column(length: 255)]
    private ?string $materia = null;

    #[ORM\Column]
    private ?int $anio = null;

    #[ORM\ManyToMany(targetEntity: Alumno::class, mappedBy: 'cursos')]
    #[ORM\OrderBy(['apellido' => 'ASC'])]
    private Collection $alumnos;

    #[ORM\ManyToOne(inversedBy: 'cursos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuario = null;

    #[ORM\ManyToOne(inversedBy: 'cursos')]
    private ?Organizacion $organizacion = null;

    #[ORM\OneToMany(mappedBy: 'curso', targetEntity: TomaDeAsistencia::class)]
    private Collection $tomasDeAsistencia;

    public function __construct()
    {
        $this->alumnos = new ArrayCollection();
        $this->tomasDeAsistencia = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGrado(): ?string
    {
        return $this->grado;
    }

    public function setGrado(string $grado): self
    {
        $this->grado = $grado;

        return $this;
    }

    public function getDivision(): ?string
    {
        return $this->division;
    }

    public function setDivision(string $division): self
    {
        $this->division = $division;

        return $this;
    }

    public function getMateria(): ?string
    {
        return $this->materia;
    }

    public function setMateria(string $materia): self
    {
        $this->materia = $materia;

        return $this;
    }

    public function getAnio(): ?int
    {
        return $this->anio;
    }

    public function setAnio(int $anio): self
    {
        $this->anio = $anio;

        return $this;
    }

    /**
     * @return Collection<int, Alumno>
     */
    public function getAlumnos(): Collection
    {
        return $this->alumnos;
    }

    public function addAlumno(Alumno $alumno): self
    {
        if (!$this->alumnos->contains($alumno)) {
            $this->alumnos->add($alumno);
            $alumno->addCurso($this);
        }

        return $this;
    }

    public function removeAlumno(Alumno $alumno): self
    {
        if ($this->alumnos->removeElement($alumno)) {
            $alumno->removeCurso($this);
        }

        return $this;
    }

    public function hasAlumno(Alumno $alumno)
    {
        foreach ($this->alumnos as $a) {
            if ($alumno->getId() == $a->getId()) {
                return true;
            }
        }
        return false;
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

    public function getOrganizacion(): ?Organizacion
    {
        return $this->organizacion;
    }

    public function setOrganizacion(?Organizacion $organizacion): self
    {
        $this->organizacion = $organizacion;

        return $this;
    }

    /**
     * @return Collection<int, TomaDeAsistencia>
     */
    public function getTomasDeAsistencia(): Collection
    {
        return $this->tomasDeAsistencia;
    }

    public function addTomasDeAsistencium(TomaDeAsistencia $tomasDeAsistencium): self
    {
        if (!$this->tomasDeAsistencia->contains($tomasDeAsistencium)) {
            $this->tomasDeAsistencia->add($tomasDeAsistencium);
            $tomasDeAsistencium->setCurso($this);
        }

        return $this;
    }

    public function removeTomasDeAsistencium(TomaDeAsistencia $tomasDeAsistencium): self
    {
        if ($this->tomasDeAsistencia->removeElement($tomasDeAsistencium)) {
            // set the owning side to null (unless already changed)
            if ($tomasDeAsistencium->getCurso() === $this) {
                $tomasDeAsistencium->setCurso(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->grado . 'º ' . $this->division . ' - ' . $this->materia . ' - ' . $this->anio;
    }
}
