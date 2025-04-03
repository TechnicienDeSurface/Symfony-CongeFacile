<?php

namespace App\Form;

use App\Entity\Person;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class MotDePasseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isManager = $options['is_manager'] ?? false;

        $inputClass = $isManager 
        ? 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2' 
        : 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-200';

        $builder
        ->add('currentPassword', PasswordType::class, [
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le mot de passe ne peut pas être vide.',
                ]),
            ],
            'label' => 'Mot de passe actuel',
            'label_attr' => [
                'class' => 'block text-gray-700 font-medium',
            ],
            'attr' => [
                'id' => 'password',
            ], 
            'mapped' => false,
            // 'attr' => [
            //     'class' => $inputClass,
            //     'disabled' => !$isManager,
            // ],
        ])
        ->add('newPassword', PasswordType::class, [
            'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le mot de passe ne peut pas être vide.',
                    ]),
                    new Assert\Length([
                        'min' => 10,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/[A-Z]/',
                        'message' => 'Le mot de passe doit contenir au moins une lettre majuscule.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/[a-z]/',
                        'message' => 'Le mot de passe doit contenir au moins une lettre minuscule.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/\d/',
                        'message' => 'Le mot de passe doit contenir au moins un chiffre.',
                    ]),
                ],
            'label' => 'Nouveau mot de passe',
            'label_attr' => [
                'class' => 'block text-gray-700 font-medium',
            ],
            // 'attr' => [
            //     'id' => 'password',
            // ], 
            'mapped' => false,
            // 'attr' => [
            //         'class' => $inputClass,
            //         'disabled' => !$isManager, // Désactiver si ce n'est pas un manager
            // ],
        ])
        ->add('confirmPassword', PasswordType::class, [
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le mot de passe ne peut pas être vide.',
                ]),
                new Assert\Length([
                    'min' => 10,
                    'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                ]),
                new Assert\Regex([
                    'pattern' => '/[A-Z]/',
                    'message' => 'Le mot de passe doit contenir au moins une lettre majuscule.',
                ]),
                new Assert\Regex([
                    'pattern' => '/[a-z]/',
                    'message' => 'Le mot de passe doit contenir au moins une lettre minuscule.',
                ]),
                new Assert\Regex([
                    'pattern' => '/\d/',
                    'message' => 'Le mot de passe doit contenir au moins un chiffre.',
                ]),
            ],
            'label' => 'Confirmer le nouveau mot de passe',
            'label_attr' => [
                'class' => 'block text-gray-700 font-medium',
            ],
            'mapped' => false,
            // 'attr' => [
            //     'class' => $inputClass,
            //     'disabled' => !$isManager, // Désactiver si ce n'est pas un manager
            // ],
        ]) 
    
        ->add('Submit', SubmitType::class, [
            'label' => 'Réinitialiser le mot de passe',
            'attr' => [
                'class' => 'w-full text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition',
                'style' => 'background-color: #004C6C;',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_manager' => false,
        ]);
    }
}
