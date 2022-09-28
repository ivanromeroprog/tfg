<?php
namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
#[UniqueEntity('email')]
#[UniqueEntity('username')]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
    
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $apellido = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $telefono = null;
/*
    #[ORM\OneToMany(mappedBy: 'usuario', targetEntity: Venta::class)]
    private Collection $Ventas;
*/
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $direccion = null;

    #[ORM\OneToMany(mappedBy: 'usuario', targetEntity: Curso::class)]
    private Collection $cursos;

    #[ORM\ManyToMany(targetEntity: Organizacion::class, mappedBy: 'usuarios')]
    private Collection $organizaciones;

    #[ORM\OneToMany(mappedBy: 'usuarioOrigen', targetEntity: Invitacion::class)]
    private Collection $invitacionesEnviadas;

    #[ORM\OneToMany(mappedBy: 'usuarioDestino', targetEntity: Invitacion::class)]
    private Collection $invitacionesRecibidas;

    #[ORM\OneToMany(mappedBy: 'usuario', targetEntity: Actividad::class)]
    private Collection $actividades;
    
    public function __construct(?int $id = null, ?string $username = null, ?string $password = null,
            ?string $email = null, ?string $Nombre = null,
            ?string $Apellido = null, ?string $Telefono = null, ?string $Direccion = null) {
        $this->id = $id;
        $this->username = $username;
        //$this->roles = $roles;
        $this->password = $password;
        $this->email = $email;
        $this->nombre = $Nombre;
        $this->apellido = $Apellido;
        $this->telefono = $Telefono;
        $this->direccion = $Direccion;
        //$this->Ventas = new ArrayCollection();
        $this->cursos = new ArrayCollection();
        $this->organizaciones = new ArrayCollection();
        $this->invitacionesEnviadas = new ArrayCollection();
        $this->invitacionesRecibidas = new ArrayCollection();
        $this->actividades = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getUsername(): ?string {
        return $this->username;
    }

    public function setUsername(string $username): self {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password): self {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials() {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(string $email): self {
        $this->email = $email;

        return $this;
    }
    
    public function getNombre(): ?string {
        return $this->nombre;
    }

    public function setNombre(string $Nombre): self {
        $this->nombre = $Nombre;

        return $this;
    }

    public function getApellido(): ?string {
        return $this->apellido;
    }

    public function setApellido(string $Apellido): self {
        $this->apellido = $Apellido;

        return $this;
    }

    public function getTelefono(): ?string {
        return $this->telefono;
    }

    public function setTelefono(string $Telefono): self {
        $this->telefono = $Telefono;

        return $this;
    }

    
    /**
     * @return Collection<int, Venta>
    public function getVentas(): Collection {
        return $this->Ventas;
    }

    public function addVenta(Venta $venta): self {
        if (!$this->Ventas->contains($venta)) {
            $this->Ventas->add($venta);
            $venta->setUsuario($this);
        }

        return $this;
    }

    public function removeVenta(Venta $venta): self {
        if ($this->Ventas->removeElement($venta)) {
            // set the owning side to null (unless already changed)
            if ($venta->getUsuario() === $this) {
                $venta->setUsuario(null);
            }
        }

        return $this;
    }
        
    */
    public function getDireccion(): ?string {
        return $this->direccion;
    }

    public function setDireccion(?string $Direccion): self {
        $this->direccion = $Direccion;

        return $this;
    }
/*
    public function isVerified(): bool
    {
        return $this->isVerified;
    }
*/
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
            $curso->setUsuario($this);
        }

        return $this;
    }

    public function removeCurso(Curso $curso): self
    {
        if ($this->cursos->removeElement($curso)) {
            // set the owning side to null (unless already changed)
            if ($curso->getUsuario() === $this) {
                $curso->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Organizacion>
     */
    public function getOrganizaciones(): Collection
    {
        return $this->organizaciones;
    }

    public function addOrganizacion(Organizacion $organizacion): self
    {
        if (!$this->organizaciones->contains($organizacion)) {
            $this->organizaciones->add($organizacion);
            $organizacion->addUsuario($this);
        }

        return $this;
    }

    public function removeOrganizacion(Organizacion $organizacion): self
    {
        if ($this->organizaciones->removeElement($organizacion)) {
            $organizacion->removeUsuario($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Invitacion>
     */
    public function getInvitacionesEnviadas(): Collection
    {
        return $this->invitacionesEnviadas;
    }

    public function addInvitacionEnviada(Invitacion $invitacionesEnviada): self
    {
        if (!$this->invitacionesEnviadas->contains($invitacionesEnviada)) {
            $this->invitacionesEnviadas->add($invitacionesEnviada);
            $invitacionesEnviada->setUsuarioOrigen($this);
        }

        return $this;
    }

    public function removeInvitacionEnviada(Invitacion $invitacionesEnviada): self
    {
        if ($this->invitacionesEnviadas->removeElement($invitacionesEnviada)) {
            // set the owning side to null (unless already changed)
            if ($invitacionesEnviada->getUsuarioOrigen() === $this) {
                $invitacionesEnviada->setUsuarioOrigen(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invitacion>
     */
    public function getInvitacionesRecibidas(): Collection
    {
        return $this->invitacionesRecibidas;
    }

    public function addInvitacionRecibida(Invitacion $invitacionesRecibida): self
    {
        if (!$this->invitacionesRecibidas->contains($invitacionesRecibida)) {
            $this->invitacionesRecibidas->add($invitacionesRecibida);
            $invitacionesRecibida->setUsuarioDestino($this);
        }

        return $this;
    }

    public function removeInvitacionRecibida(Invitacion $invitacionesRecibida): self
    {
        if ($this->invitacionesRecibidas->removeElement($invitacionesRecibida)) {
            // set the owning side to null (unless already changed)
            if ($invitacionesRecibida->getUsuarioDestino() === $this) {
                $invitacionesRecibida->setUsuarioDestino(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Actividad>
     */
    public function getActividades(): Collection
    {
        return $this->actividades;
    }

    public function addActividade(Actividad $actividade): self
    {
        if (!$this->actividades->contains($actividade)) {
            $this->actividades->add($actividade);
            $actividade->setUsuario($this);
        }

        return $this;
    }

    public function removeActividade(Actividad $actividade): self
    {
        if ($this->actividades->removeElement($actividade)) {
            // set the owning side to null (unless already changed)
            if ($actividade->getUsuario() === $this) {
                $actividade->setUsuario(null);
            }
        }

        return $this;
    }

}
