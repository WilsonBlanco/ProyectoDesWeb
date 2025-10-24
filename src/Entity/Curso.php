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

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $descripcion = null;

    #[ORM\OneToMany(mappedBy: 'curso', targetEntity: CursoCarreraSemestre::class)]
    private Collection $cursoCarreraSemestres;

    #[ORM\OneToMany(mappedBy: 'curso', targetEntity: Inscripcion::class)]
    private Collection $inscripciones;

    #[ORM\OneToMany(mappedBy: 'curso', targetEntity: Nota::class)]
    private Collection $notas;

    public function __construct()
    {
        $this->cursoCarreraSemestres = new ArrayCollection();
        $this->inscripciones = new ArrayCollection();
        $this->notas = new ArrayCollection();
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
            $cursoCarreraSemestre->setCurso($this);
        }

        return $this;
    }

    public function removeCursoCarreraSemestre(CursoCarreraSemestre $cursoCarreraSemestre): static
    {
        if ($this->cursoCarreraSemestres->removeElement($cursoCarreraSemestre)) {
            // set the owning side to null (unless already changed)
            if ($cursoCarreraSemestre->getCurso() === $this) {
                $cursoCarreraSemestre->setCurso(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Inscripcion>
     */
    public function getInscripciones(): Collection
    {
        return $this->inscripciones;
    }

    public function addInscripcion(Inscripcion $inscripcion): static
    {
        if (!$this->inscripciones->contains($inscripcion)) {
            $this->inscripciones->add($inscripcion);
            $inscripcion->setCurso($this);
        }

        return $this;
    }

    public function removeInscripcion(Inscripcion $inscripcion): static
    {
        if ($this->inscripciones->removeElement($inscripcion)) {
            // set the owning side to null (unless already changed)
            if ($inscripcion->getCurso() === $this) {
                $inscripcion->setCurso(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Nota>
     */
    public function getNotas(): Collection
    {
        return $this->notas;
    }

    public function addNota(Nota $nota): static
    {
        if (!$this->notas->contains($nota)) {
            $this->notas->add($nota);
            $nota->setCurso($this);
        }

        return $this;
    }

    public function removeNota(Nota $nota): static
    {
        if ($this->notas->removeElement($nota)) {
            // set the owning side to null (unless already changed)
            if ($nota->getCurso() === $this) {
                $nota->setCurso(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->nombre ?? '';
    }
}