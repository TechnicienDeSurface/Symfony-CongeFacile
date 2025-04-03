<?php

namespace App\Form;

use App\Entity\Department;
use App\Entity\Person;
use App\Entity\Position;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InformationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isManager = $options['is_manager'] ?? false;

        $inputClass = $isManager 
        ? 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2' 
        : 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-200';


        $builder
        ->add('last_name', TextType::class, [
            'label' => 'Nom de famille',
            'label_attr' => [
                'class' => 'block text-gray-700 font-medium',
            ],
            'attr' => [
                'class' => 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-200',
                'value' => 'Martins',
                'disabled' => 'disabled'
            ],
        ])
        ->add('first_name', TextType::class, [
            'label' => 'Prénom',
            'label_attr' => [
                'class' => 'block text-gray-700 font-medium',
            ],
            'attr' => [
                'class' => 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-200',
                'value' => 'Jeff',
                'disabled' => 'disabled'
            ],
        ])
        ->add('email', TextType::class, [
            'label' => 'Adresse email du manager',
            'label_attr' => [
                'class' => 'block text-gray-700 font-medium',
            ],
            'mapped' => false,
            'attr' => [
                'class' => 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-200',
                'readonly' => true,
                'value'=>'email.example@gmail.com',
                'disabled' => 'disabled'
            ],
        ])
        ->add('department', EntityType::class, [
            'class' => Department::class,
            'choice_label' => 'name',
            'label' => 'Direction/Service',
            'label_attr' => [
                'class' => 'block text-gray-700 font-medium',
            ],
            'attr' => [
                'class' => 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-200',
                'disabled' => 'disabled',
            ],
        ]);
        if ($options['is_manager'] == false) {
            $builder->add('position_id', EntityType::class, [
                'class' => Position::class,
                'choice_label' => 'name',
                'label' => 'Poste',
                'label_attr' => [
                    'class' => 'block text-gray-700 font-medium',
                ],
                'attr' => [
                    'class' => 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-200',
                    'disabled' => 'disabled',
                ],
            ])
            ->add('manager_id', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'firstnamelastname',
                'label' => 'Manager',
                'label_attr' => [
                    'class' => 'block text-gray-700 font-medium',
                ],
                'attr' => [
                    'class' => 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-200',
                    'disabled' => 'disabled',
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
            'is_manager' => false, //SERT POUR DIRE SI C'EST INFORMATION MANAGER OU PAS
        ]);
    }
}
