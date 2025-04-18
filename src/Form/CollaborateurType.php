<?php

namespace App\Form;

use App\Entity\Position; 
use App\Entity\Department; 
use App\Repository\PositionRepository; 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class CollaborateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $inputClass = 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500';
        $labelClass = 'block text-sm font-medium text-gray-700';

        $builder
            // Prénom
            ->add('first_name', TextType::class, [
                'label' => 'Prénom - champ obligatoire',
                'label_attr' => ['class' => $labelClass],
                'attr' => ['class' => $inputClass],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prénom est obligatoire']),
                ],
            ])

            // Nom de famille
            ->add('last_name', TextType::class, [
                'label' => 'Nom de famille - champ obligatoire',
                'label_attr' => ['class' => $labelClass],
                'attr' => ['class' => $inputClass],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom de famille est obligatoire']),
                ],
            ])

            // Direction / Département
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'choice_label' => 'name',
                'label' => 'Direction/Service - champ obligatoire',
                'label_attr' => ['class' => $labelClass],
                'attr' => ['class' => $inputClass],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le département est obligatoire']),
                ],
            ])
            ->add('position', EntityType::class, [
                'class' => Position::class,
                'choice_label' => 'name',
                'label' => 'Poste - champ obligatoire',
                'label_attr' => ['class' => $labelClass],
                'attr' => ['class' => $inputClass],
            ])
            // Adresse email
            ->add('email', EmailType::class, [
                'label' => 'Adresse email - champ obligatoire',
                'label_attr' => ['class' => $labelClass],
                'attr' => ['class' => $inputClass],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'adresse email est obligatoire']),
                ],
            ])

            // Nouveau mot de passe
            ->add('newPassword', PasswordType::class, [
                'label' => 'Nouveau mot de passe - champ obligatoire',
                'label_attr' => ['class' => $labelClass],
                'attr' => ['class' => $inputClass],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le mot de passe ne peut pas être vide.']),
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
            ])

            // Confirmation du mot de passe
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Confirmation de mot de passe - champ obligatoire',
                'label_attr' => ['class' => $labelClass],
                'attr' => ['class' => $inputClass],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le mot de passe ne peut pas être vide.']),
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
            ])

            // Bouton de soumission
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter',
                'attr' => [
                    'class' => 'w-full text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition',
                    'style' => 'background-color: #004C6C;',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
