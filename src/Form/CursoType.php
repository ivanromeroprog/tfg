<?php

namespace App\Form;

use App\Entity\Alumno;
use App\Entity\Curso;
use App\Entity\Organizacion;
use App\Repository\AlumnoRepository;
use App\Repository\OrganizacionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CursoType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('grado', TextType::class, ['label' => 'Grado o Año', 'disabled' => $options['view']])
            ->add('division', TextType::class, ['label' => 'División', 'disabled' => $options['view']])
            ->add('materia', TextType::class, ['disabled' => $options['view']])
            ->add('anio', NumberType::class, ['label' => 'Año Lectivo', 'html5' => true, 'scale' => 0, 'attr' => ['min' => '1990', 'max' => '999999999', 'step' => '1'], 'disabled' => $options['view']]);

        $builder->add(
            'organizacion',
            EntityType::class,
            [
                'class' => Organizacion::class,
                'query_builder' => function (OrganizacionRepository $er) use ($options) {
                    return $er->createQueryBuilder('o')
                        ->innerJoin('o.usuarios', 'u')
                        ->where('u = :usuario')
                        ->setParameter('usuario', $options['usuario']);
                },
                'autocomplete' => true,
                'label' => 'Organización (Escuela, Instituto)',
                //'required' => !$options['modify'],
                'disabled' => $options['view'] || $options['modify'],
                'help' => 'La organización no puede ser modificada una vez creado el curso. Si necesita hacerlo debe crear un nuevo curso.'
            ]
        );

        // TODO: Agregar carga de alumnos con ajax
        if ($options['modify'] || $options['view']) {
            $builder->add(
                'alumnos',
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
                    /*
                    'help' => $options['modify'] ? 'Escriba para buscar alumnos de la organización y agregarlos a este curso.'
                        . '<br>Si encuentra algun error en los datos puede'
                        . ' <a href="#" data-bs-toggle="collapse" data-bs-target="#modificar_alumno_form" aria-expanded="false"'
                        . ' aria-controls="modificar_alumno_form">modificar los datos de un alumno</a>.'
                        . '<br>Si no encuentra el alumno puede'
                        . ' <a href="#" data-bs-toggle="collapse" data-bs-target="#agregar_alumno_form" aria-expanded="false"'
                        . ' aria-controls="agregar_alumno_form modificar_alumno_form">agregar uno nuevo</a>.' : null,
                     *                      */
                    'help' => $options['modify'] ? 'Escriba para buscar alumnos de la organización y agregarlos a este curso.'
                        . '<br>Si encuentra algun error en los datos puede'
                        . ' <a href="javascript:null" id="modificar_alumno_link" data-action="click->alumno#mostrar_modificar_alumno_e">modificar los datos de un alumno</a>.'
                        . '<br>Si no encuentra el alumno puede'
                        . ' <a href="javascript:null" id="agregar_alumno_link" data-action="click->alumno#mostrar_agregar_alumno_e">agregar uno nuevo</a>.' : null,
                    'help_html' => true,
                    'query_builder' => function (AlumnoRepository $ar) use ($options) {
                        return $ar->createQueryBuilder('a')
                            ->innerJoin('a.cursos', 'c')
                            ->innerJoin('c.organizacion', 'o')
                            ->where('o = :organizacion')
                            ->orderBy('a.apellido', 'ASC')
                            ->setParameter('organizacion', $options['organizacion']);
                    },
                    'attr' => $options['modify'] ? ['data-action' => 'change->alumno#cargar_options_alumno_modificar'] : []
                ]
            );
        }

        if ($options['modify']) {

            //Modificar
            $builder
                ->add(
                    'alumno_mod_id',
                    EntityType::class,
                    [
                        'label' => $options['modify'] ? false : "Alumno a Modificar",
                        'class' => Alumno::class,
                        'expanded' => false,
                        'multiple' => false,
                        'disabled' => $options['view'],
                        'by_reference' => false,
                        'autocomplete' => false,
                        'mapped' => false,
                        'required' => true,
                        'attr' => [
                            'data-action' => 'change->alumno#cargar_alumno_modificar'
                        ]
                    ]
                )
                ->add('alumno_mod_nombre', TextType::class, ['mapped' => false, 'label' => 'Nombre/s', 'required' => true])
                ->add('alumno_mod_apellido', TextType::class, ['mapped' => false, 'label' => 'Apellido/s', 'required' => true])
                ->add(
                    'alumno_mod_cua',
                    TextType::class,
                    [
                        'mapped' => false,
                        'label' => 'CUA <a href="javascript:null" data-bs-toggle="tooltip" data-bs-placement="top" title="Código Único de Alumno: puede ser el Nº de DNI o de Matrícula.">?</a>',
                        'label_html' => true,
                        'required' => true
                    ]
                )
                //->add('alumno_agregar', SubmitType::class, ['label' => '<i class="bi bi-plus-circle"></i>', 'label_html' => true]);
                ->add('alumno_modificar', SubmitType::class, ['label' => 'Modificar', 'attr' => ['class' => 'btn btn-success']]);

            //Agregar
            $builder
                ->add('alumno_nombre', TextType::class, ['mapped' => false, 'label' => 'Nombre/s', 'required' => true])
                ->add('alumno_apellido', TextType::class, ['mapped' => false, 'label' => 'Apellido/s', 'required' => true])
                ->add(
                    'alumno_cua',
                    TextType::class,
                    [
                        'mapped' => false,
                        'label' => 'CUA <a href="javascript:null" data-bs-toggle="tooltip" data-bs-placement="top" title="Código Único de Alumno: puede ser el Nº de DNI o de Matrícula.">?</a>',
                        'label_html' => true,
                        'required' => true
                    ]
                )
                //->add('alumno_agregar', SubmitType::class, ['label' => '<i class="bi bi-plus-circle"></i>', 'label_html' => true]);
                ->add('alumno_agregar', SubmitType::class, ['label' => 'Agregar', 'attr' => ['class' => 'btn btn-success']]);
        }

        if (!$options['view']) {
            $builder->add('Submit', SubmitType::class, ['label' => 'Guardar', 'attr' => ['style' => "float:right;"]]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Curso::class,
            'view' => false,
            'modify' => false,
            'organizacion' => null,
            'usuario' => null
        ]);
    }
}
