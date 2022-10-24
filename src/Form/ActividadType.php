<?php

namespace App\Form;

use App\Entity\Actividad;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\NotBlank;

class ActividadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //Solo permitir los valores de tipo válidos
        $options['tipo'] = in_array($options['tipo'], Actividad::TIPOS) ? $options['tipo'] : null;

        $builder->add('titulo', TextType::class, [
            'label' => 'Título',
            'required' => true,
            'constraints' => [
                new NotBlank()
            ]
        ])
            ->add('descripcion', TextareaType::class, ['label' => 'Descripción', 'required' =>  false]);

        //Si no hay tipo, permitir seleccionar
        if (is_null($options['tipo'])) {

            //Agregar un elemento falso invisible
            $builder->add('tipofake', HiddenType::class, ['label' => 'Tipo', 'disabled' => true, 'mapped' => false, 'data' => $options['tipo']]);
            $builder->add('tipo', ChoiceType::class, ['label' => 'Tipo', 'choices' => Actividad::TIPOS]);

            $submitlabel = 'Siguiente';
        } else {

            //Agregar un elemento invisible para mostrar el deshabilitado
            $builder->add('tipofake', TextType::class, ['label' => 'Tipo', 'disabled' => true, 'mapped' => false, 'data' => $options['tipo']]);
            $builder->add('tipo', HiddenType::class, ['label' => 'Tipo']);

            $submitlabel = 'Guardar';
        }

        $builder->add('Submit', SubmitType::class, ['label' => $submitlabel, 'attr' => ['style' => 'float: right']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Actividad::class,
            'tipo' => null,
        ]);
    }
}
