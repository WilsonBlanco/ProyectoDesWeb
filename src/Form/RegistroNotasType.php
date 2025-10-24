<?php

namespace App\Form;

use App\Entity\Carrera;
use App\Entity\Curso;
use App\Entity\Seccion;
use App\Entity\Semestre;
use App\Repository\CursoRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistroNotasType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('carrera', EntityType::class, [
                'class' => Carrera::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Seleccione una carrera',
                'attr' => ['class' => 'form-control'],
                'label' => 'Carrera'
            ])
            ->add('semestre', EntityType::class, [
                'class' => Semestre::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Seleccione un semestre',
                'attr' => ['class' => 'form-control'],
                'label' => 'Semestre'
            ])
            ->add('curso', EntityType::class, [
                'class' => Curso::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Seleccione un curso',
                'attr' => ['class' => 'form-control'],
                'label' => 'Curso'
            ])
            ->add('seccion', EntityType::class, [
                'class' => Seccion::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Seleccione una sección',
                'attr' => ['class' => 'form-control'],
                'label' => 'Sección'
            ])
        ;

        // Agregar dinámicamente el campo de notas ANTES de que se envíe el formulario
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            if ($data && isset($data['carrera']) && isset($data['curso']) && isset($data['seccion']) && isset($data['semestre'])) {
                // Este campo se llenará dinámicamente en el controller
                // No necesitamos agregar un campo CollectionType aquí porque
                // las notas se manejarán directamente en el controller
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