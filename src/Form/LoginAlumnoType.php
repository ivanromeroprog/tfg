<?php

namespace App\Form;

use App\Entity\Alumno;
use App\Repository\AlumnoRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginAlumnoType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'alumno',
                EntityType::class,
                [
                    'label' => "Alumno",
                    'class' => Alumno::class,
                    'expanded' => false,
                    'multiple' => false,
                    //'disabled' => $options['view'],
                    'by_reference' => false,
                    'autocomplete' => true,
                    'required' => false,
                    'help' => 'Busca o selecciona tu nombre de la lista de alumnso del curso.',
                    //'help_html' => true,
                    'choice_label' => function ($alumno) {
                        return $alumno->getApellido() . ', ' . $alumno->getNombre();
                    },

                    'query_builder' => function (AlumnoRepository $ar) use ($options) {
                        return $ar->createQueryBuilder('a')
                            ->innerJoin('a.cursos', 'c')
                            //->innerJoin('c.organizacion', 'o')
                            ->where('c = :curso')
                            ->orderBy('a.apellido', 'ASC')
                            ->setParameter('curso', $options['curso']);
                    },
                    'attr' => ['autocomplete' => 'nono']
                ]
            )
            ->add('cua', \Symfony\Component\Form\Extension\Core\Type\PasswordType::class, [
                'label' => 'CUA',
                'help' => 'Código Único de Alumno: Si no lo conoces comunicate con tu docente.', 'attr' => ['autocomplete' => 'new-password']
            ])
            ->add('Sumbit', SubmitType::class, ['label' => 'Ingresar']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'curso' => null
        ]);
    }
}
