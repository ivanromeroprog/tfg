<?php

namespace App\Form;

use App\Entity\Usuario;
use App\Entity\Organizacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /*
        $builder
            ->add('username')
                
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
        */
        $builder
            ->add(
                'organizacion',
                EntityType::class,
                [
                    'label' => 'Organización (Escuela, Instituto)',
                    'class' => Organizacion::class,
                    'by_reference' => false,
                    'autocomplete' => true,
                    //'multiple' => true,
                    'mapped' => false
                ]
            )
            ->add('rol', ChoiceType::class, ['disabled' => true, 'mapped' => false, 'label' => '¿Qué rol cumple en la organización?', 'choices' => ['Docente' => 'ROLE_DOCENTE', 'Responsable de la Organización (Directivo, coordinador, etc.)' => 'ROLE_RESPONSABLE']])
            ->add('username', TextType::class, ['label' => 'Nombre de Usuario', 'disabled' => $options['view']])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Los campos de Clave deben ser iguales.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => ['label' => 'Clave'],
                'second_options' => ['label' => 'Repetir Clave'],
                'disabled' => $options['view'],
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'minMessage' => 'La clave debe tener almenos {{ limit }} caracteres',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    //regex -> to have at elast one digit
                    new Regex([
                        'pattern' => '/\d+/i',
                        'message' => 'La clave debe contener al menos un dígito',
                    ]),
                    //regex -> to have at elast one special char from the list
                    //note: list of special-char is [#?!@$%^&*-]. Adjust to suite your needs
                    new Regex([
                        'pattern' => '/[#?!@$%^&*-]+/i',
                        'message' => 'La clave debe contener al menos un carácter especial de los siguientes: # ? ! @ $ % ^ & * -)',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, ['disabled' => $options['view']])
            //->add('Dni', NumberType::class, ['html5' => true, 'scale' => 0, 'attr' => ['min' => '0'], 'disabled' => $options['view']])
            ->add('Nombre', TextType::class, ['disabled' => $options['view']])
            ->add('Apellido', TextType::class, ['disabled' => $options['view']])
            ->add('Telefono', TelType::class, ['required' => false, 'label' => 'Teléfono', 'disabled' => $options['view']])
            ->add('Direccion', TextType::class, ['required' => false, 'label' => 'Dirección', 'disabled' => $options['view']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
            'required_password' => true,
            'view' => false
        ]);
    }
}
