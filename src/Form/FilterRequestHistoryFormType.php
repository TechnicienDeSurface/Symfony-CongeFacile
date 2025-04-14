<?php

namespace App\Form;

use App\Entity\Person;
use App\Entity\RequestType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class FilterRequestHistoryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isManager = $options['is_manager'] ?? false;

        $builder
            ->add('request_type', EntityType::class, [
                'class' => RequestType::class,
                'choice_label' => 'name',
                'label' => 'Type de demande',
                'required' => false,
                'placeholder' => 'Sélectionner un statut...',
                'attr' => ['class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline']
            ]);
            if ($isManager === true) {
                $builder->add('collaborator', EntityType::class, [
                    'class' => Person::class,
                    'choice_label' => 'firstnamelastname',
                    'label' => 'Collaborateur',
                    'required' => false,
                    'placeholder' => 'Sélectionner un collaborateur...',
                    'attr' => [
                        'class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline'
                    ],
                ]);
            } else {
                $builder->add('created_at', DateType::class, [
                    'label' => 'Demandée le ',
                    'required' => false,
                    'widget' => 'single_text',
                    'attr' => [
                        'class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline'
                    ],
                ]);
            }
            
            $builder
            ->add('start_at', DateType::class, [
                'label' => 'Date de début',
                'required' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline']
            ])
            ->add('end_at', DateType::class, [
                'label' => 'Date de fin',
                'required' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline']
            ])
            ->add('nbdays', ChoiceType::class, [
                'label' => 'Nombre de jours',
                'required' => false,
                'choices' => [
                    'Croissant' => 'ASC',
                    'Décroissant' => 'DESC',
                ],
                'placeholder' => 'Choisir l\'ordre...',
                'attr' => ['class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline']
            ])
            ->add('answer', ChoiceType::class, [
                'label' => 'Statut',
                'required' => false,
                'choices' => [
                    'Validé' => '1',
                    'Refusé' => '0',
                    'En attente' => null,
                ],
                'placeholder' => 'Sélectionner un statut...',
                'attr' => ['class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Filtrer les demandes',
                'attr' => [
                    'class' => 'w-full text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition',
                    'style' => 'background-color: #004C6C;'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null, // On ne filtre pas sur un objet Request directement
            'is_manager' => false,
        ]);
    }
}
