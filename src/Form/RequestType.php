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
use Symfony\Component\Form\Extension\Core\Type\FileType ; 
use Symfony\Component\Form\Extension\Core\Type\DateType ; 
use Symfony\Component\Form\Extension\Core\Type\NumberType ; 
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
                'attr' => ['class' => 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2'],
            ])
            ->add('end_at', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin',
                'attr' => ['class' => 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2'],
            ])
            ->add('working_days', NumberType::class, [
                'label' => 'Nombre de jours ouvrés',
                'mapped' => false,
                
                'attr' => ['class' => 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2'],
                // 'attr' => ['class' => 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-100', 'id' => 'working_days', 'readonly' => true],
            ])
            ->add('receipt_file', FileType::class, [
                'label' => 'Justificatif si applicable',
                'attr' => ['class' => 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2'],
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Commentaire',
                'attr' => [
                    'class' => 'form-textarea mt-1 block w-full border border-gray-300 rounded-md p-2',
                    'placeholder' => 'Si congé exceptionnel ou sans solde, vous pouvez préciser votre demande.',
                    'rows' => 5, // Augmente la hauteur du champ
                ],
            ])
            ->add('request_type', EntityType::class, [
                'class' => RequestTypeEntity::class,
                'choice_label' => 'id',
                'label' => 'Type de demande',
                'attr' => ['class' => 'form-select mt-1 block w-full border border-gray-300 rounded-md p-2'],
            ])
            ->add('Submit', SubmitType::class); 
            }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Request::class,
        ]);
    }
}
