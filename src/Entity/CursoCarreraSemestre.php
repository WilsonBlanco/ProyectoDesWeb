<?php

namespace App\Entity;

use App\Repository\CursoCarreraSemestreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CursoCarreraSemestreRepository::class)]
#[ORM\UniqueConstraint(name: 'unique_curso_carrera_semestre', columns: ['curso_id', 'carrera_id', 'semestre_id'])]
class CursoCarreraSemestre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Curso::class, inversedBy: 'cursoCarreraSemestres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Curso $curso = null;

    #[ORM\ManyToOne(targetEntity: Carrera::class, inversedBy: 'cursoCarreraSemestres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Carrera $carrera = null;

    #[ORM\ManyToOne(targetEntity: Semestre::class, inversedBy: 'cursoCarreraSemestres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Semestre $semestre = null;

    #[ORM\OneToMany(mappedBy: 'cursoCarreraSemestre', targetEntity: CursoSeccion::class)]
    private Collection $cursoSecciones;

    #[ORM\OneToMany(mappedBy: 'cursoCarreraSemestre', targetEntity: Inscripcion::class)]
    private Collection $inscripciones;

    public function __construct()
    {
        $this->cursoSecciones = new ArrayCollection();
        $this->inscripciones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurso(): ?Curso
    {
        return $this->curso;
    }

    public function setCurso(?Curso $curso): static
    {
        $this->curso = $curso;

        return $this;
    }

    public function getCarrera(): ?Carrera
    {
        return $this->carrera;
    }

    public function setCarrera(?Carrera $carrera): static
    {
        $this->carrera = $carrera;

        return $this;
    }

    public function getSemestre(): ?Semestre
    {
        return $this->semestre;
    }

    public function setSemestre(?Semestre $semestre): static
    {
        $this->semestre = $semestre;

        return $this;
    }

    /**
     * @return Collection<int, CursoSeccion>
     */
    public function getCursoSecciones(): Collection
    {
        return $this->cursoSecciones;
    }

    public function addCursoSeccion(CursoSeccion $cursoSeccion): static
    {
        if (!$this->cursoSecciones->contains($cursoSeccion)) {
            $this->cursoSecciones->add($cursoSeccion);
            $cursoSeccion->setCursoCarreraSemestre($this);
        }

        return $this;
    }

    public function removeCursoSeccion(CursoSeccion $cursoSeccion): static
    {
        if ($this->cursoSecciones->removeElement($cursoSeccion)) {
            // set the owning side to null (unless already changed)
            if ($cursoSeccion->getCursoCarreraSemestre() === $this) {
                $cursoSeccion->setCursoCarreraSemestre(null);
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
            $inscripcion->setCursoCarreraSemestre($this);
        }

        return $this;
    }

    public function removeInscripcion(Inscripcion $inscripcion): static
    {
        if ($this->inscripciones->removeElement($inscripcion)) {
            // set the owning side to null (unless already changed)
            if ($inscripcion->getCursoCarreraSemestre() === $this) {
                $inscripcion->setCursoCarreraSemestre(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%s - %s - %s', 
            $this->curso?->getNombre(), 
            $this->carrera?->getNombre(), 
            $this->semestre?->getNombre()
        );
    }
}