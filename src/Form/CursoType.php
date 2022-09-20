<?php

namespace App\Form;

use App\Entity\Alumno;
use App\Entity\Curso;
use App\Entity\Organizacion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CursoType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
                ->add('grado', TextType::class, ['disabled' => $options['view']])
                ->add('division', TextType::class, ['disabled' => $options['view']])
                ->add('materia', TextType::class, ['disabled' => $options['view']])
                ->add('anio', NumberType::class, ['html5' => true, 'scale' => 0, 'attr' => ['min' => '1990', 'max' => '999999999', 'step' => '1'], 'disabled' => $options['view']])
                ->add('alumnos',
                        EntityType::class,
                        [
                            'class' => Alumno::class,
                            'expanded' => false,
                            'multiple' => true,
                        //'attr' => ['class' => 'js-choice']
                        ])
                //->add('usuario')
                ->add('organizacion',
                        EntityType::class,
                        [
                            'class' => Organizacion::class,
                            'attr' => ['class' => 'js-choice']
                        ])
        ;
        if (!$options['view'])
            $builder->add('Submit', SubmitType::class, ['label' => 'Guardar', 'attr' => ['style' => "float:right;"]]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Curso::class,
            'view' => false,
        ]);
    }

}
