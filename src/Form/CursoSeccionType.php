<?php

namespace App\Form;

use App\Entity\CursoSeccion;
use App\Entity\CursoCarreraSemestre;
use App\Entity\Seccion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CursoSeccionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cursoCarreraSemestre', EntityType::class, [
                'class' => CursoCarreraSemestre::class,
                'choice_label' => function (CursoCarreraSemestre $ccs) {
                    return sprintf('%s - %s - %s', 
                        $ccs->getCurso()->getNombre(),
                        $ccs->getCarrera()->getNombre(),
                        $ccs->getSemestre()->getNombre()
                    );
                },
                'placeholder' => 'Seleccione un curso de carrera y semestre',
                'attr' => ['class' => 'form-control']
            ])
            ->add('seccion', EntityType::class, [
                'class' => Seccion::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Seleccione una sección',
                'attr' => ['class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CursoSeccion::class,
        ]);
    }
}