<?php

namespace App\Form;

use DateTime;
use Dom\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\RequestType;
use App\Entity\Request;
use App\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class FilterRequestPendingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $filters = $options['filters']; // Récupère les filtres passés

        $builder
            ->add('request_type', EntityType::class, [
                'class' => RequestType::class,
                'choice_label' => 'name',
                'label' => 'Type de demande :',
                'required' => false,
                'data' => $filters['request_type'] ?? null, // Remplir avec la valeur du filtre ou null
                'attr' => ['class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline']
            ])
            ->add('created_at', DateType::class, [
                'label' => 'Demandé le :',
                'required' => false,
                'data' => isset($filters['created_at']) ? new \DateTime($filters['created_at']) : null, // Remplir avec la valeur de created_at
                'attr' => ['class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline']
            ])
            ->add('collaborator', EntityType::class, [
                'class' => \App\Entity\Person::class,
                'choices' => $options['collaborators'], // Liste filtrée des collaborateurs
                'choice_label' => function (\App\Entity\Person $person) {
                    return $person->getFirstName() . ' ' . $person->getLastName();
                },
                'label' => 'Collaborateur :',
                'required' => false,
                'placeholder' => 'Tous les collaborateurs',
                'data' => isset($filters['collaborator']) ? $filters['collaborator'] : null, // Remplir avec la valeur de collaborator
                'attr' => ['class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline']
            ])
            ->add('start_at', DateType::class, [
                'label' => 'Date de début :',
                'required' => false,
                'data' => isset($filters['start_at']) ? new \DateTime($filters['start_at']) : null, // Remplir avec la valeur de start_at
                'attr' => ['class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline']
            ])
            ->add('end_at', DateType::class, [
                'label' => 'Date de fin :',
                'required' => false,
                'data' => isset($filters['end_at']) ? new \DateTime($filters['end_at']) : null, // Remplir avec la valeur de end_at
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
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => ['class' => 'bg-[#004c6c] hover:bg-blue-700 text-white font-bold rounded py-2 px-6'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'collaborators' => [],
            'filters' => [],
        ]);
    }
}
