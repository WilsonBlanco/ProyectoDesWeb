<?php

namespace App\Entity;

use App\Repository\CarreraRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarreraRepository::class)]
class Carrera
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $descripcion = null;

    #[ORM\OneToMany(mappedBy: 'carrera', targetEntity: Alumno::class)]
    private Collection $alumnos;

    #[ORM\OneToMany(mappedBy: 'carrera', targetEntity: CursoCarreraSemestre::class)]
    private Collection $cursoCarreraSemestres;

    public function __construct()
    {
        $this->alumnos = new ArrayCollection();
        $this->cursoCarreraSemestres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): static
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * @return Collection<int, Alumno>
     */
    public function getAlumnos(): Collection
    {
        return $this->alumnos;
    }

    public function addAlumno(Alumno $alumno): static
    {
        if (!$this->alumnos->contains($alumno)) {
            $this->alumnos->add($alumno);
            $alumno->setCarrera($this);
        }

        return $this;
    }

    public function removeAlumno(Alumno $alumno): static
    {
        if ($this->alumnos->removeElement($alumno)) {
            // set the owning side to null (unless already changed)
            if ($alumno->getCarrera() === $this) {
                $alumno->setCarrera(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CursoCarreraSemestre>
     */
    public function getCursoCarreraSemestres(): Collection
    {
        return $this->cursoCarreraSemestres;
    }

    public function addCursoCarreraSemestre(CursoCarreraSemestre $cursoCarreraSemestre): static
    {
        if (!$this->cursoCarreraSemestres->contains($cursoCarreraSemestre)) {
            $this->cursoCarreraSemestres->add($cursoCarreraSemestre);
            $cursoCarreraSemestre->setCarrera($this);
        }

        return $this;
    }

    public function removeCursoCarreraSemestre(CursoCarreraSemestre $cursoCarreraSemestre): static
    {
        if ($this->cursoCarreraSemestres->removeElement($cursoCarreraSemestre)) {
            // set the owning side to null (unless already changed)
            if ($cursoCarreraSemestre->getCarrera() === $this) {
                $cursoCarreraSemestre->setCarrera(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->nombre ?? '';
    }
}