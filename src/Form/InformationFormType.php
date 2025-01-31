<?php

namespace App\Form;

use App\Entity\Department;
use App\Entity\Person;
use App\Entity\Position;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InformationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('last_name', TextType::class, [
            'label' => 'Nom de famille',
            'attr' => [
                'class' => 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-200',
                'value' => 'Martins',
                'disabled' => 'disabled'
            ],
        ])
        ->add('first_name', TextType::class, [
            'label' => 'Prénom',
            'attr' => [
                'class' => 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-200',
                'value' => 'Jeff',
                'disabled' => 'disabled'
            ],
        ])
        ->add('manager_id', EntityType::class, [
            'class' => User::class, // Remplace UserContact par l'entité correcte
            'choice_label' => 'email',
            'label' => 'Adresse email',
            'attr' => [
                'class' => 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-200',
                'disabled' => 'disabled'
            ],
        ])
        ->add('department_id', EntityType::class, [
            'class' => Department::class,
            'choice_label' => 'name',
            'label' => 'Direction/Service',
            'attr' => [
                'class' => 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-200',
                'disabled' => 'disabled'
            ],
        ])
        ->add('position_id', EntityType::class, [
            'class' => Position::class,
            'choice_label' => 'name',
            'label' => 'Poste',
            'attr' => [
                'class' => 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-200',
                'disabled' => 'disabled'
            ],
        ])
        ->add('manager_id', EntityType::class, [
            'class' => User::class,
            'choice_label' => 'name',
            'label' => 'Manager',
            'attr' => [
                'class' => 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-200',
                'disabled' => 'disabled'
            ],
        ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
        ]);
    }
}
