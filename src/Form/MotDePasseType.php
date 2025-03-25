<?php

namespace App\Form;

use App\Entity\Person;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            'label' => 'Mot de passe actuel',
            'label_attr' => [
                'class' => 'block text-gray-700 font-medium',
            ],
            'mapped' => false,
            'attr' => [
                'class' => $inputClass,
                'disabled' => !$isManager,
            ],
        ])
        ->add('newPassword', PasswordType::class, [
            'label' => 'Nouveau mot de passe',
            'label_attr' => [
                'class' => 'block text-gray-700 font-medium',
            ],
            'mapped' => false,
            'attr' => [
                    'class' => $inputClass,
                    'disabled' => !$isManager, // Désactiver si ce n'est pas un manager
            ],
        ])
        ->add('confirmPassword', PasswordType::class, [
            'label' => 'Confirmer le nouveau mot de passe',
            'label_attr' => [
                'class' => 'block text-gray-700 font-medium',
            ],
            'mapped' => false,
            'attr' => [
                'class' => $inputClass,
                'disabled' => !$isManager, // Désactiver si ce n'est pas un manager
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
