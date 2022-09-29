<?php

namespace App\Form;

use App\Entity\Alumno;
use App\Entity\Curso;
use App\Entity\Organizacion;
use App\Repository\OrganizacionRepository;
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
        
        // TODO: filtrar alumnos solo para los cursos de la misma organización
        // TODO: Agregar carga de alumnos con ajax
        if ($options['modify'] || $options['view']) {
            $builder->add('alumnos',
                    EntityType::class,
                    [
                        'label' => $options['modify'] ? false : "Alumnos",
                        'class' => Alumno::class,
                        'expanded' => false,
                        'multiple' => true,
                        'disabled' => $options['view'],
                        'by_reference' => false,
                        'autocomplete' => true,
                        'required' => false,
                    //'attr' => ['class' => 'js-choice']
            ]);
        }
        if($options['modify']){
                    $builder
            ->add('alumno_nombre', TextType::class,['mapped' => false, 'label' => 'Nombre/s', 'required'=>true])
            ->add('alumno_apellido', TextType::class,['mapped' => false, 'label'=>'Apellido/s', 'required'=>true])
            ->add('alumno_cua', TextType::class,['mapped' => false, 'label'=>'CUA <a href="#">?</a>', 'label_html'=>true, 'required'=>true])
            ->add('alumno_agregar', SubmitType::class, ['label' => '<i class="bi bi-plus-circle"></i>','label_html' => true]);
            //$builder->add('alumnos', AlumnoType::class);
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
