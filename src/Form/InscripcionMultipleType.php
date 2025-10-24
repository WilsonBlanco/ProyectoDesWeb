<?php

namespace App\Form;

use App\Entity\Alumno;
use App\Entity\Carrera;
use App\Entity\Semestre;
use App\Entity\CursoSeccion;
use App\Repository\AlumnoRepository;
use App\Repository\CursoSeccionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InscripcionMultipleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('carrera', EntityType::class, [
                'class' => Carrera::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Seleccione una carrera',
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'label' => 'Carrera'
            ])
            ->add('semestre', EntityType::class, [
                'class' => Semestre::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Seleccione un semestre',
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'label' => 'Semestre'
            ])
        ;

        // Listener para cargar alumnos cuando se selecciona una carrera
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            
            // Campo de alumnos inicialmente vacío
            $form->add('alumnos', EntityType::class, [
                'class' => Alumno::class,
                'choice_label' => function (Alumno $alumno) {
                    return sprintf('%s %s', $alumno->getNombres(), $alumno->getApellidos());
                },
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'size' => 10
                ],
                'label' => 'Alumnos'
            ]);

            // Campo de curso sección inicialmente vacío
            $form->add('cursoSeccion', EntityType::class, [
                'class' => CursoSeccion::class,
                'choice_label' => function (CursoSeccion $cursoSeccion) {
                    return sprintf('%s - Sección %s', 
                        $cursoSeccion->getCursoCarreraSemestre()->getCurso()->getNombre(),
                        $cursoSeccion->getSeccion()->getNombre()
                    );
                },
                'placeholder' => 'Primero seleccione carrera y semestre',
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'label' => 'Curso y Sección'
            ]);
        });

        // Listener para actualizar alumnos cuando se selecciona una carrera
        $builder->get('carrera')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm()->getParent();
            $carrera = $event->getForm()->getData();

            if ($form) {
                $form->add('alumnos', EntityType::class, [
                    'class' => Alumno::class,
                    'choice_label' => function (Alumno $alumno) {
                        return sprintf('%s %s', $alumno->getNombres(), $alumno->getApellidos());
                    },
                    'query_builder' => function (AlumnoRepository $repository) use ($carrera) {
                        if (!$carrera) {
                            return $repository->createQueryBuilder('a')
                                ->where('1 = 0'); // No mostrar alumnos si no hay carrera seleccionada
                        }
                        
                        return $repository->createQueryBuilder('a')
                            ->where('a.carrera = :carrera')
                            ->setParameter('carrera', $carrera)
                            ->orderBy('a.apellidos', 'ASC')
                            ->addOrderBy('a.nombres', 'ASC');
                    },
                    'multiple' => true,
                    'expanded' => false,
                    'required' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'size' => 10
                    ],
                    'label' => 'Alumnos'
                ]);
            }
        });

        // Listener para actualizar cursos cuando se seleccionan carrera y semestre
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            $carreraId = $data['carrera'] ?? null;
            $semestreId = $data['semestre'] ?? null;

            if ($carreraId && $semestreId) {
                $form->add('cursoSeccion', EntityType::class, [
                    'class' => CursoSeccion::class,
                    'choice_label' => function (CursoSeccion $cursoSeccion) {
                        return sprintf('%s - Sección %s', 
                            $cursoSeccion->getCursoCarreraSemestre()->getCurso()->getNombre(),
                            $cursoSeccion->getSeccion()->getNombre()
                        );
                    },
                    'query_builder' => function (CursoSeccionRepository $repository) use ($carreraId, $semestreId) {
                        return $repository->createQueryBuilder('cs')
                            ->join('cs.cursoCarreraSemestre', 'ccs')
                            ->join('ccs.carrera', 'c')
                            ->join('ccs.semestre', 's')
                            ->where('c.id = :carreraId')
                            ->andWhere('s.id = :semestreId')
                            ->setParameter('carreraId', $carreraId)
                            ->setParameter('semestreId', $semestreId)
                            ->orderBy('ccs.curso', 'ASC');
                    },
                    'placeholder' => 'Seleccione un curso y sección',
                    'attr' => ['class' => 'form-control'],
                    'label' => 'Curso y Sección'
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // No necesita data_class porque es un formulario sin entidad específica
        ]);
    }
}