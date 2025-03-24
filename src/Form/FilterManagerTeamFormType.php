<?php 
// src/Form/FilterType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FilterManagerTeamFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('last_name', TextType::class, [
                'label' => 'Nom :',
                'required' => false,
                'attr' => ['class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline']
            ])
            ->add('first_name', TextType::class, [
                'label' => 'Prénom :',
                'required' => false,
                'attr' => ['class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email :',
                'required' => false,
                'attr' => ['class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline']
            ])
            ->add('name', TextType::class, [
                'label' => 'Poste :',
                'required' => false,
                'attr' => ['class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline']
            ])
            ->add('totalleavedays', TextType::class, [
                'label' => 'Nb de congés posés sur l\'année :',
                'required' => false,
                'attr' => ['class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => ['class' => 'bg-[#004c6c] hover:bg-blue-700 text-white font-bold rounded py-2 px-6'],
            ]);
    }
}