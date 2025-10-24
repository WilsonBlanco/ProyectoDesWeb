<?php

namespace App\Form;

use App\Entity\CursoCarreraSemestre;
use App\Entity\Carrera;
use App\Entity\Curso;
use App\Entity\Semestre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CursoCarreraSemestreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('carrera', EntityType::class, [
                'class' => Carrera::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Seleccione una carrera',
                'attr' => ['class' => 'form-control']
            ])
            ->add('semestre', EntityType::class, [
                'class' => Semestre::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Seleccione un semestre',
                'attr' => ['class' => 'form-control']
            ])
            ->add('curso', EntityType::class, [
                'class' => Curso::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Seleccione un curso',
                'attr' => ['class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CursoCarreraSemestre::class,
        ]);
    }
}