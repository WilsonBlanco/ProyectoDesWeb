<?php

namespace App\Entity;

use App\Repository\InscripcionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InscripcionRepository::class)]
#[ORM\UniqueConstraint(name: 'unique_alumno_curso_seccion', columns: ['alumno_id', 'curso_seccion_id'])]
class Inscripcion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Alumno::class, inversedBy: 'inscripciones')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Alumno $alumno = null;

    #[ORM\ManyToOne(targetEntity: CursoSeccion::class, inversedBy: 'inscripciones')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CursoSeccion $cursoSeccion = null;

    #[ORM\ManyToOne(targetEntity: CursoCarreraSemestre::class, inversedBy: 'inscripciones')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CursoCarreraSemestre $cursoCarreraSemestre = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $fechaInscripcion = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $activo = true;

    public function __construct()
    {
        $this->fechaInscripcion = new \DateTime();
        $this->activo = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAlumno(): ?Alumno
    {
        return $this->alumno;
    }

    public function setAlumno(?Alumno $alumno): static
    {
        $this->alumno = $alumno;

        return $this;
    }

    public function getCursoSeccion(): ?CursoSeccion
    {
        return $this->cursoSeccion;
    }

    public function setCursoSeccion(?CursoSeccion $cursoSeccion): static
    {
        $this->cursoSeccion = $cursoSeccion;

        // Establecer automáticamente el cursoCarreraSemestre
        if ($cursoSeccion !== null) {
            $this->cursoCarreraSemestre = $cursoSeccion->getCursoCarreraSemestre();
        }

        return $this;
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

    public function getFechaInscripcion(): ?\DateTimeInterface
    {
        return $this->fechaInscripcion;
    }

    public function setFechaInscripcion(\DateTimeInterface $fechaInscripcion): static
    {
        $this->fechaInscripcion = $fechaInscripcion;

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

    // Métodos helper para acceder a relaciones anidadas
    public function getCurso(): ?Curso
    {
        return $this->cursoCarreraSemestre?->getCurso();
    }

    public function getSeccion(): ?Seccion
    {
        return $this->cursoSeccion?->getSeccion();
    }

    public function getSemestre(): ?Semestre
    {
        return $this->cursoCarreraSemestre?->getSemestre();
    }

    public function getCarrera(): ?Carrera
    {
        return $this->cursoCarreraSemestre?->getCarrera();
    }

    public function __toString(): string
    {
        return sprintf('%s - %s', 
            $this->alumno?->getNombreCompleto(),
            $this->getCurso()?->getNombre()
        );
    }
}