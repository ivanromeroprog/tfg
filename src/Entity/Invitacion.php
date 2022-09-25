<?php

namespace App\Entity;

use App\Repository\InvitacionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvitacionRepository::class)]
class Invitacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Organizacion $organizacion = null;

    #[ORM\ManyToOne(inversedBy: 'invitacionesEnviadas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuarioOrigen = null;

    #[ORM\ManyToOne(inversedBy: 'invitacionesRecibidas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuarioDestino = null;

    #[ORM\Column(length: 255)]
    private ?string $rol = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUsuarioOrigen(): ?Usuario
    {
        return $this->usuarioOrigen;
    }

    public function setUsuarioOrigen(?Usuario $usuarioOrigen): self
    {
        $this->usuarioOrigen = $usuarioOrigen;

        return $this;
    }

    public function getUsuarioDestino(): ?Usuario
    {
        return $this->usuarioDestino;
    }

    public function setUsuarioDestino(?Usuario $usuarioDestino): self
    {
        $this->usuarioDestino = $usuarioDestino;

        return $this;
    }

    public function getRol(): ?string
    {
        return $this->rol;
    }

    public function setRol(string $rol): self
    {
        $this->rol = $rol;

        return $this;
    }
}
