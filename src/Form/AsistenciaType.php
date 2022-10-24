<?php

namespace App\Form;

use App\Entity\Curso;
use App\Entity\TomaDeAsistencia;
use App\Repository\CursoRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\NotBlank;

class AsistenciaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'curso',
                EntityType::class,
                [
                    'class' => Curso::class,
                    'label' => 'Clase',
                    'multiple' => false,
                    //'disabled' => $options['view'],
                    'required' => true,
                    'constraints' => [new NotBlank()],
                    //'by_reference' => false,
                    'autocomplete' => !$options['modify'],
                    'query_builder' => function (CursoRepository $er) use ($options) {
                        return $er->createQueryBuilder('c')
                            ->innerJoin('c.usuario', 'u')
                            ->setParameter('usuario', $options['usuario'])
                            ->where('u = :usuario');
                    },
                    //'attr' => ['required'=>'required']
                    //'required' => !$options['modify'],
                    'disabled' => $options['view'] || $options['modify'],
                    //'help' => 'La organización no puede ser modificada una vez creado el curso. Si necesita hacerlo debe crear un nuevo curso.'
                ]
            )
            ->add(
                'fecha',
                DateTimeType::class,
                [
                    'html5' => true,
                    'widget' => 'single_text',
                    'required' => true,
                    'constraints' => [new NotBlank()],
                    'attr' => [
                        'placeholder' => 'aaaa-mm-ddThh:mm:ss',
                        //'data-controller' => 'datetimefix',
                        'step' => 1 //SIN ESTA LINEA NO FUNCIONAN LOS SEGUNDOS
                    ],
                    'disabled' => $options['view'] || $options['modify']
                ]
            );

        if ($options['modify']) {

            $builder
                ->add('estado', ChoiceType::class, [
                    'choices' => TomaDeAsistencia::ESTADOS,
                    'disabled' => true,
                    'help_html' => true,
                    'help' => '<em>Iniciado</em>: la toma de asistencia esta en progreso y los alumnos pueden dar el presente.<br>'
                        . '<em>Finalizado</em>: finalizo la toma de asistencia y los alumnos ya no pueden dar el presente.<br>'
                        . '<em>Anulado</em>: finalizo la toma de asistencia y los datos registrados aquí no serán tomados en cuanta al generar informes.<br>'
                ]);

            if ($options['pregunta'] == 'anular') {
                $builder->add(TomaDeAsistencia::ESTADO_ANULADO, SubmitType::class, ['label' => 'Anular', 'attr' => ['class' => 'btn-danger', 'value' => 'Anular', 'style' => "float:right;"]]);
            } elseif ($options['pregunta'] == 'finalizar') {
                $builder->add(TomaDeAsistencia::ESTADO_FINALIZADO, SubmitType::class, ['label' => 'Finalizar', 'attr' => ['style' => "float:right;", 'value' => 'Finalizar']]);
            } elseif ($options['pregunta'] == 'iniciar') {
                $builder->add(TomaDeAsistencia::ESTADO_INICIADO, SubmitType::class, ['label' => 'Iniciar', 'attr' => ['class' => 'btn-success', 'style' => "float:right;", 'value' => 'Re-Iniciar']]);
            }
        } else {
            $builder->add('Submit', SubmitType::class, ['label' => 'Iniciar', 'attr' => ['style' => "float:right;"]]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TomaDeAsistencia::class,
            'usuario' => null,
            'view' => false,
            'modify' => false,
            'pregunta' => ''
        ]);
    }
}
