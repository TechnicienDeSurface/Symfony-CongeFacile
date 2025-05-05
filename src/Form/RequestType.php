<?php

namespace App\Form;

use App\Entity\Department;
use App\Entity\Person;
use App\Entity\Request;
use App\Entity\RequestType as RequestTypeEntity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType as BaseCountryType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('collaborator', EntityType::class, [
                'class' => Person::class,
                'choice_label' => 'first_name',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le collaborateur est obligatoire']),
                    new Assert\Type(['type' => Person::class, 'message' => 'Valeur invalide pour un collaborateur']),
                ],
                'attr' => ['style' => 'display:none'],
                'label_attr' => ['style' => 'display:none;']
            ])
            ->add('start_at', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime', // important pour que Symfony convertisse en DateTime
                'label' => 'Date et heure de début',
                'html5' => true,
                'attr' => ['class' => 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La date de début est obligatoire']),
                    // new Assert\DateTime(['message' => 'La date de début doit être une date et heure valide']), renvoie this value should be of type string sur une date
                    new Assert\LessThan([
                        'propertyPath' => 'parent.all[end_at].data',
                        'message' => 'La date de début doit être antérieure à la date de fin',
                    ]),
                ],
            ])
            ->add('end_at', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'label' => 'Date et heure de fin',
                'html5' => true,
                'attr' => ['class' => 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La date de fin est obligatoire']),
                    // new Assert\DateTime(['message' => 'La date de fin doit être une date et heure valide']), renvoie this value should be of type string sur une date
                    new Assert\GreaterThan([
                        'propertyPath' => 'parent.all[start_at].data',
                        'message' => 'La date de fin doit être postérieure à la date de début',
                    ]),
                ],
            ])
            
            ->add('working_days', NumberType::class, [
                'label' => 'Nombre de jours ouvrés',
                'mapped' => false,
                'attr' => ['class' => 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nombre de jours ouvrés est obligatoire']),
                    new Assert\Positive(['message' => 'Le nombre de jours ouvrés doit être supérieur à 0']),
                    new Assert\LessThanOrEqual([
                        'value' => 365,
                        'message' => 'Le nombre de jours ouvrés ne peut pas dépasser 365 jours',
                    ])
                ],
            ])
            ->add('receipt_file', FileType::class, [
                'label' => 'Justificatif si applicable',
                'attr' => ['class' => 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2'],
                'required' => false,
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier PDF valide',
                        'maxSizeMessage' => 'Le fichier ne doit pas dépasser 1 Mo',
                    ]),
                ],
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Commentaire',
                'attr' => [
                    'class' => 'form-textarea mt-1 block w-full border border-gray-300 rounded-md p-2',
                    'placeholder' => 'Si congé exceptionnel ou sans solde, vous pouvez préciser votre demande.',
                    'rows' => 5,
                ],
                'required' => false,
                'constraints' => [
                    new Assert\Type(['type' => 'string', 'message' => 'Le commentaire doit être une chaîne de caractères']),
                    new Assert\Length([
                        'max' => 1000,
                        'maxMessage' => 'Le commentaire ne peut pas dépasser 1000 caractères',
                    ]),
                ],
            ])
            ->add('request_type', EntityType::class, [
                'class' => RequestTypeEntity::class,
                'choice_label' => 'name',
                'label' => 'Type de demande',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le type de demande est obligatoire']),
                    new Assert\Type(['type' => RequestTypeEntity::class, 'message' => 'Valeur invalide pour un type de demande']),
                ],
            ])
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'choice_label' => 'name',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le département est obligatoire']),
                    new Assert\Type(['type' => Department::class, 'message' => 'Valeur invalide pour un département']),
                ],
                'attr' => ['style' => 'display:none'],
                'label_attr' => ['style' => 'display:none;']
            ])
            ->add('Submit', SubmitType::class, [
                'label' => 'Soumettre la demande de congé',
                'attr' => [
                    'class' => 'w-full text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition',
                    'style' => 'background-color: #004C6C;',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Request::class,
        ]);
    }
}
