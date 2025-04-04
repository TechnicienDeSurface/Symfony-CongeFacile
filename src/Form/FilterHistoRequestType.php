<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class FilterHistoRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('request_type', TextType::class, [
                'label'=>'Type de demande',
                'required'=>false, 
            ])
            ->add('collaborator', TextType::class, [
                'label'=>'Collaborateur',
                'required'=>false, 
            ])
            ->add('start_at',DateType::class, [
                'widget' => 'single_text',
                'label'=>'Début à',
                'required'=>false, 
            ])
            ->add('end_at', DateType::class, [
                'widget' => 'single_text',
                'label'=>'Fin à',
                'required'=>false, 
            ])
            ->add('totalnbdemande', ChoiceType::class, [
                'label' => 'Nb demandes associées :',
                'required' => false,
                'choices' => [
                    'Croissant' => 'ASC',
                    'Décroissant' => 'DESC',
                ],
                'attr' => ['class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline'],
                'placeholder' => 'Choisir l\'ordre',
            ])
            ->add('answer', ChoiceType::class, [
                'label' => 'Statut :',
                'required' => false,
                'mapped' => false,
                'choices' => [
                    'Validé' => 'Validé',
                    'Refusé' => 'Refusé',
                    'En attente' => 'En attente',
                ],
                'attr' => ['class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline'],
                'placeholder' => 'Choisir l\'ordre',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => ['class' => 'bg-[#004c6c] hover:bg-blue-700 text-white font-bold rounded py-2 px-6'],
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
