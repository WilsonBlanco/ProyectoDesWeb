<?php

namespace App\Entity;

use App\Repository\CursoSeccionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CursoSeccionRepository::class)]
#[ORM\UniqueConstraint(name: 'unique_curso_seccion', columns: ['curso_carrera_semestre_id', 'seccion_id'])]
class CursoSeccion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: CursoCarreraSemestre::class, inversedBy: 'cursoSecciones')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CursoCarreraSemestre $cursoCarreraSemestre = null;

    #[ORM\ManyToOne(targetEntity: Seccion::class, inversedBy: 'cursoSecciones')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Seccion $seccion = null;

    #[ORM\OneToMany(mappedBy: 'cursoSeccion', targetEntity: Inscripcion::class)]
    private Collection $inscripciones;

    public function __construct()
    {
        $this->inscripciones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCursoCarreraSemestre(): ?CursoCarreraSemestre
    {
        return $this->cursoCarreraSemestre;
    }

    public function setCursoCarreraSemestre(?CursoCarreraSemestre $cursoCarreraSemestre): static
    {
        $this->cursoCarreraSemestre = $cursoCarreraSemestre;

        return $this;
    }

    public function getSeccion(): ?Seccion
    {
        return $this->seccion;
    }

    public function setSeccion(?Seccion $seccion): static
    {
        $this->seccion = $seccion;

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
            $inscripcion->setCursoSeccion($this);
        }

        return $this;
    }

    public function removeInscripcion(Inscripcion $inscripcion): static
    {
        if ($this->inscripciones->removeElement($inscripcion)) {
            // set the owning side to null (unless already changed)
            if ($inscripcion->getCursoSeccion() === $this) {
                $inscripcion->setCursoSeccion(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%s - Sección %s', 
            $this->cursoCarreraSemestre?->getCurso()?->getNombre(),
            $this->seccion?->getNombre()
        );
    }
}