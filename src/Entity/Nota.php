<?php

namespace App\Entity;

use App\Repository\NotaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotaRepository::class)]
#[ORM\UniqueConstraint(name: 'unique_alumno_curso_semestre', columns: ['alumno_id', 'curso_id', 'semestre_id'])]
class Nota
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Alumno::class, inversedBy: 'notas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Alumno $alumno = null;

    #[ORM\ManyToOne(targetEntity: Curso::class, inversedBy: 'notas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Curso $curso = null;

    #[ORM\ManyToOne(targetEntity: Seccion::class, inversedBy: 'notas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Seccion $seccion = null;

    #[ORM\ManyToOne(targetEntity: Semestre::class, inversedBy: 'notas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Semestre $semestre = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private ?string $calificacion = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $fechaRegistro = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $aprobado = false;

    public function __construct()
    {
        $this->fechaRegistro = new \DateTime();
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

    public function getCurso(): ?Curso
    {
        return $this->curso;
    }

    public function setCurso(?Curso $curso): static
    {
        $this->curso = $curso;

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

    public function getSemestre(): ?Semestre
    {
        return $this->semestre;
    }

    public function setSemestre(?Semestre $semestre): static
    {
        $this->semestre = $semestre;

        return $this;
    }

    public function getCalificacion(): ?string
    {
        return $this->calificacion;
    }

    public function setCalificacion(string $calificacion): static
    {
        $this->calificacion = $calificacion;
        // Calcular automáticamente si está aprobado (nota mínima 61)
        $this->aprobado = (float) $calificacion >= 61;

        return $this;
    }

    public function getFechaRegistro(): ?\DateTimeInterface
    {
        return $this->fechaRegistro;
    }

    public function setFechaRegistro(\DateTimeInterface $fechaRegistro): static
    {
        $this->fechaRegistro = $fechaRegistro;

        return $this;
    }

    public function isAprobado(): ?bool
    {
        return $this->aprobado;
    }

    public function setAprobado(bool $aprobado): static
    {
        $this->aprobado = $aprobado;

        return $this;
    }

    public function getEstado(): string
    {
        return $this->aprobado ? 'Aprobado' : 'Reprobado';
    }

    public function __toString(): string
    {
        return sprintf('%s - %s: %s', 
            $this->alumno?->getNombreCompleto(),
            $this->curso?->getNombre(),
            $this->calificacion
        );
    }
}