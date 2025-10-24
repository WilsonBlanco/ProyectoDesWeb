<?php

namespace App\Form;

use App\Entity\Inscripcion;
use App\Entity\Alumno;
use App\Entity\CursoSeccion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InscripcionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('alumno', EntityType::class, [
                'class' => Alumno::class,
                'choice_label' => function (Alumno $alumno) {
                    return sprintf('%s %s - %s', 
                        $alumno->getNombres(),
                        $alumno->getApellidos(),
                        $alumno->getCarrera() ? $alumno->getCarrera()->getNombre() : 'Sin carrera'
                    );
                },
                'placeholder' => 'Seleccione un alumno',
                'attr' => ['class' => 'form-control']
            ])
            ->add('cursoSeccion', EntityType::class, [
                'class' => CursoSeccion::class,
                'choice_label' => function (CursoSeccion $cursoSeccion) {
                    return sprintf('%s - %s - %s - Sección %s', 
                        $cursoSeccion->getCursoCarreraSemestre()->getCurso()->getNombre(),
                        $cursoSeccion->getCursoCarreraSemestre()->getCarrera()->getNombre(),
                        $cursoSeccion->getCursoCarreraSemestre()->getSemestre()->getNombre(),
                        $cursoSeccion->getSeccion()->getNombre()
                    );
                },
                'placeholder' => 'Seleccione un curso y sección',
                'attr' => ['class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Inscripcion::class,
        ]);
    }
}