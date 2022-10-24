<?php

namespace App\Form;

use App\Entity\PresentacionActividad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PresentacionActividadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('estado')
            ->add('fecha')
            ->add('titulo')
            ->add('descripcion')
            ->add('curso')
            ->add('usuario')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PresentacionActividad::class,
        ]);
    }
}
