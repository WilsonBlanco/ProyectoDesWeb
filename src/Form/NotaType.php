<?php

namespace App\Form;

use App\Entity\Alumno;
use App\Entity\Curso;
use App\Entity\Nota;
use App\Entity\Seccion;
use App\Entity\Semestre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('calificacion')
            ->add('fechaRegistro')
            ->add('aprobado')
            ->add('alumno', EntityType::class, [
                'class' => Alumno::class,
                'choice_label' => 'id',
            ])
            ->add('curso', EntityType::class, [
                'class' => Curso::class,
                'choice_label' => 'id',
            ])
            ->add('seccion', EntityType::class, [
                'class' => Seccion::class,
                'choice_label' => 'id',
            ])
            ->add('semestre', EntityType::class, [
                'class' => Semestre::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Nota::class,
        ]);
    }
}
