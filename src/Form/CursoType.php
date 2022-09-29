<?php

namespace App\Form;

use App\Entity\Alumno;
use App\Entity\Curso;
use App\Entity\Organizacion;
use App\Repository\OrganizacionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
                ->add('anio', NumberType::class, ['label' => 'Año Lectivo', 'html5' => true, 'scale' => 0, 'attr' => ['min' => '1990', 'max' => '999999999', 'step' => '1'], 'disabled' => $options['view']]);
        //->add('usuario')

        $builder->add('organizacion',
                EntityType::class,
                [
                    'class' => Organizacion::class,
                    'attr' => ['class' => 'js-choice'],
                    'query_builder' => function (OrganizacionRepository $er) use ($options) {
                        return $er->createQueryBuilder('o')
                                ->innerJoin('o.usuarios','u')
                                ->where('u = :usuario')
                                ->setParameter('usuario', $options['usuario']);
                    },
                    //'required' => !$options['modify'],
                    'disabled' => $options['view'] || $options['modify']
        ]);

        if ($options['modify'] || $options['view']) {
            $builder->add('alumnos',
                    EntityType::class,
                    [
                        'label' => 'Alumnos',
                        'class' => Alumno::class,
                        'expanded' => false,
                        'multiple' => true,
                        'disabled' => $options['view'],
                        'autocomplete' => true,
                    //'attr' => ['class' => 'js-choice']
            ]);
            $builder->add('alumnos', CollectionType::class);
        }



        if (!$options['view']) {
            $builder->add('Submit', SubmitType::class, ['label' => 'Guardar', 'attr' => ['style' => "float:right;"]]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Curso::class,
            'view' => false,
            'modify' => false,
            'usuario' => null
        ]);
    }

}
