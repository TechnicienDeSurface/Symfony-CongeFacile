<?php

namespace App\Form;

use App\Entity\Department;
use App\Entity\Person;
use App\Entity\Request;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType ; 
use Symfony\Component\Form\Extension\Core\Type\DateType ; 
use Symfony\Component\Form\Extension\Core\Type\MoneyType ; 
use Symfony\Component\Form\Extension\Core\Type\CountryType as BaseCountryType;
use App\Entity\RequestType as RequestTypeEntity; // Ajoutez cette ligne pour utiliser la bonne entité

class RequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start_at', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de début',
                'attr' => ['class' => 'form-input mt-1 block w-full', 'id' => 'start_at'],
            ])
            ->add('end_at', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin',
                'attr' => ['class' => 'form-input mt-1 block w-full', 'id' => 'end_at'],
            ])
            ->add('answer_at', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de réponse',
                'attr' => ['class' => 'form-input mt-1 block w-full', 'id' => 'answer_at'],
            ])
            ->add('receipt_file', TextType::class, [
                'label' => 'Fichier de reçu',
                'attr' => ['class' => 'form-input mt-1 block w-full'],
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Commentaire',
                'attr' => ['class' => 'form-textarea mt-1 block w-full'],
            ])
            ->add('answer_comment', TextareaType::class, [
                'label' => 'Commentaire de réponse',
                'attr' => ['class' => 'form-textarea mt-1 block w-full'],
            ])
            ->add('answer', TextType::class, [
                'label' => 'Réponse',
                'attr' => ['class' => 'form-input mt-1 block w-full'],
            ])
            ->add('working_days', TextType::class, [
                'label' => 'Nombre de jours ouvrés',
                'mapped' => false, // Indique que ce champ n'est pas mappé à une propriété de l'entité
                'attr' => ['class' => 'form-input mt-1 block w-full', 'id' => 'working_days', 'readonly' => true],
            ])
            ->add('collaborator', EntityType::class, [
                'class' => Person::class,
                'choice_label' => 'id',
                'label' => 'Collaborateur',
                'attr' => ['class' => 'form-select mt-1 block w-full'],
            ])
            ->add('request_type', EntityType::class, [
                'class' => RequestTypeEntity::class,
                'choice_label' => 'id',
                'label' => 'Type de demande',
                'attr' => ['class' => 'form-select mt-1 block w-full'],
            ])
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'choice_label' => 'id',
                'label' => 'Département',
                'attr' => ['class' => 'form-select mt-1 block w-full'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Request::class,
        ]);
    }
}
