<?php

namespace App\Entity;

use App\Repository\SeccionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SeccionRepository::class)]
class Seccion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $nombre = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\OneToMany(mappedBy: 'seccion', targetEntity: CursoSeccion::class)]
    private Collection $cursoSecciones;

    #[ORM\OneToMany(mappedBy: 'seccion', targetEntity: Inscripcion::class)]
    private Collection $inscripciones;

    #[ORM\OneToMany(mappedBy: 'seccion', targetEntity: Nota::class)]
    private Collection $notas;

    public function __construct()
    {
        $this->cursoSecciones = new ArrayCollection();
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
            $cursoSeccion->setSeccion($this);
        }

        return $this;
    }

    public function removeCursoSeccion(CursoSeccion $cursoSeccion): static
    {
        if ($this->cursoSecciones->removeElement($cursoSeccion)) {
            // set the owning side to null (unless already changed)
            if ($cursoSeccion->getSeccion() === $this) {
                $cursoSeccion->setSeccion(null);
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
            $inscripcion->setSeccion($this);
        }

        return $this;
    }

    public function removeInscripcion(Inscripcion $inscripcion): static
    {
        if ($this->inscripciones->removeElement($inscripcion)) {
            // set the owning side to null (unless already changed)
            if ($inscripcion->getSeccion() === $this) {
                $inscripcion->setSeccion(null);
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
            $nota->setSeccion($this);
        }

        return $this;
    }

    public function removeNota(Nota $nota): static
    {
        if ($this->notas->removeElement($nota)) {
            // set the owning side to null (unless already changed)
            if ($nota->getSeccion() === $this) {
                $nota->setSeccion(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->nombre ?? '';
    }
}