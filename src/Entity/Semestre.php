<?php

namespace App\Entity;

use App\Repository\SemestreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SemestreRepository::class)]
class Semestre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nombre = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $fechaInicio = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $fechaFin = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $activo = false;

    #[ORM\OneToMany(mappedBy: 'semestre', targetEntity: CursoCarreraSemestre::class)]
    private Collection $cursoCarreraSemestres;

    #[ORM\OneToMany(mappedBy: 'semestre', targetEntity: Inscripcion::class)]
    private Collection $inscripciones;

    #[ORM\OneToMany(mappedBy: 'semestre', targetEntity: Nota::class)]
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

    public function getFechaInicio(): ?\DateTimeInterface
    {
        return $this->fechaInicio;
    }

    public function setFechaInicio(\DateTimeInterface $fechaInicio): static
    {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    public function getFechaFin(): ?\DateTimeInterface
    {
        return $this->fechaFin;
    }

    public function setFechaFin(\DateTimeInterface $fechaFin): static
    {
        $this->fechaFin = $fechaFin;

        return $this;
    }

    public function isActivo(): ?bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): static
    {
        $this->activo = $activo;

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
            $cursoCarreraSemestre->setSemestre($this);
        }

        return $this;
    }

    public function removeCursoCarreraSemestre(CursoCarreraSemestre $cursoCarreraSemestre): static
    {
        if ($this->cursoCarreraSemestres->removeElement($cursoCarreraSemestre)) {
            // set the owning side to null (unless already changed)
            if ($cursoCarreraSemestre->getSemestre() === $this) {
                $cursoCarreraSemestre->setSemestre(null);
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
            $inscripcion->setSemestre($this);
        }

        return $this;
    }

    public function removeInscripcion(Inscripcion $inscripcion): static
    {
        if ($this->inscripciones->removeElement($inscripcion)) {
            // set the owning side to null (unless already changed)
            if ($inscripcion->getSemestre() === $this) {
                $inscripcion->setSemestre(null);
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
            $nota->setSemestre($this);
        }

        return $this;
    }

    public function removeNota(Nota $nota): static
    {
        if ($this->notas->removeElement($nota)) {
            // set the owning side to null (unless already changed)
            if ($nota->getSemestre() === $this) {
                $nota->setSemestre(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->nombre ?? '';
    }
}