<?php

namespace App\Repository;

use App\Entity\Inscripcion;
use App\Entity\Carrera;
use App\Entity\Curso;
use App\Entity\Seccion;
use App\Entity\Semestre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Inscripcion>
 */
class InscripcionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inscripcion::class);
    }

    public function findByCarreraCursoSeccionSemestre(
        Carrera $carrera,
        Curso $curso,
        Seccion $seccion,
        Semestre $semestre
    ): array {
        return $this->createQueryBuilder('i')
            ->join('i.cursoSeccion', 'cs')
            ->join('cs.cursoCarreraSemestre', 'ccs')
            ->join('ccs.carrera', 'c')
            ->join('ccs.curso', 'cur')
            ->join('ccs.semestre', 's')
            ->join('cs.seccion', 'sec')
            ->join('i.alumno', 'a') // JOIN CORREGIDO: agregar esta línea
            ->where('c = :carrera')
            ->andWhere('cur = :curso')
            ->andWhere('sec = :seccion')
            ->andWhere('s = :semestre')
            ->andWhere('i.activo = :activo')
            ->setParameter('carrera', $carrera)
            ->setParameter('curso', $curso)
            ->setParameter('seccion', $seccion)
            ->setParameter('semestre', $semestre)
            ->setParameter('activo', true)
            ->orderBy('a.apellidos', 'ASC') // CORREGIDO: usar 'a' en lugar de 'i.alumno'
            ->addOrderBy('a.nombres', 'ASC') // CORREGIDO: usar 'a' en lugar de 'i.alumno'
            ->getQuery()
            ->getResult();
    }

    public function findNotasByAlumno(int $alumnoId): array
    {
        return $this->createQueryBuilder('i')
            ->select('i', 'a', 'c', 'cur', 's', 'sec', 'n')
            ->join('i.alumno', 'a')
            ->join('i.cursoSeccion', 'cs')
            ->join('cs.cursoCarreraSemestre', 'ccs')
            ->join('ccs.curso', 'cur')
            ->join('ccs.carrera', 'c')
            ->join('ccs.semestre', 's')
            ->join('cs.seccion', 'sec')
            ->leftJoin('App\Entity\Nota', 'n', 'WITH', 
                'n.alumno = a AND n.curso = cur AND n.seccion = sec AND n.semestre = s')
            ->where('a.id = :alumnoId')
            ->setParameter('alumnoId', $alumnoId)
            ->orderBy('s.fechaInicio', 'DESC')
            ->addOrderBy('cur.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }
}