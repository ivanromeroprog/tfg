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
                ])
                ->add('Submit', SubmitType::class, ['label' => 'Finalizar', 'attr' => ['style' => "float:right;"]]);
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
        ]);
    }
}
