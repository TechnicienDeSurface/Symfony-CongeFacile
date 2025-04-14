<?php

namespace App\Form;

use App\Entity\Department;
use App\Entity\Person;
use App\Entity\Request;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints as Assert;
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
            ->add('collaborator', EntityType::class, [
                'class' => Person::class,
                'choice_label' => 'first_name',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le collaborateur est obligatoire']),
                ],
                'attr' => ['style' => 'display:none'],
                'label_attr' => ['style' => 'display:none;']
            ])
            ->add('start_at', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de début',
                'attr' => ['class' => 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La date de début est obligatoire']),
                    // new Assert\Date(['message' => 'La date de début doit être une date valide']),
                ],
            ])
            ->add('end_at', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin',
                'attr' => ['class' => 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La date de fin est obligatoire']),
                    // new Assert\Date(['message' => 'La date de fin doit être une date valide']),
                ],
            ])
            ->add('working_days', NumberType::class, [
                'label' => 'Nombre de jours ouvrés',
                'mapped' => false,
                'attr' => ['class' => 'form-input mt-1 block w-full border border-gray-300 rounded-md p-2'],
                'constraints' => [
                    new Assert\Positive(['message' => 'Le nombre de jours ouvrés doit être positif']),
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
                    //new Assert\Type(['type' => \RequestType, 'message' => 'Le type de demande doit être une chaîne de caractères']),
                ],
            ])
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'choice_label' => 'name',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le département est obligatoire']),
                    //new Assert\Type(['type' => \RequestType, 'message' => 'Le type de demande doit être une chaîne de caractères']),
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
