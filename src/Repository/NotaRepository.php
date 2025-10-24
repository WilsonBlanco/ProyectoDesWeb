<?php

namespace App\Repository;

use App\Entity\Nota;
use App\Entity\Curso;
use App\Entity\Carrera;
use App\Entity\Seccion;
use App\Entity\Semestre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Nota>
 */
class NotaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Nota::class);
    }

    public function findByCursoCarreraSeccionSemestre(
        Curso $curso,
        Carrera $carrera,
        Seccion $seccion,
        Semestre $semestre
    ): array {
        return $this->createQueryBuilder('n')
            ->join('n.alumno', 'a')
            ->join('a.carrera', 'c')
            ->where('n.curso = :curso')
            ->andWhere('c = :carrera')
            ->andWhere('n.seccion = :seccion')
            ->andWhere('n.semestre = :semestre')
            ->setParameter('curso', $curso)
            ->setParameter('carrera', $carrera)
            ->setParameter('seccion', $seccion)
            ->setParameter('semestre', $semestre)
            ->orderBy('a.apellidos', 'ASC')
            ->addOrderBy('a.nombres', 'ASC')
            ->getQuery()
            ->getResult();
    }
}