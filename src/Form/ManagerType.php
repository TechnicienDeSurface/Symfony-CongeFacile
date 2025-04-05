<?php

namespace App\Form;

use App\Entity\Department; 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ManagerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name', TextType::class, [
                'label'=>'Prénom',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prénom est obligatoire']),
                ],
            ])
            ->add('last_name', TextType::class, [
                'label'=>'Nom de famille',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom de famille est obligatoire']),
                ],
            ])
            ->add('direction', EntityType::class, [
                'class' => Department::class,
                'choice_label' => 'name',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le département est obligatoire']),
                ],
            ])
            ->add('email', TextType::class,[
                'label'=>'Adresse email',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'adresse email est obligatoire']),
                ],
            ])
            ->add('submit', SubmitType::class,[
                'label'=>'Enregistrer le nouveau manager'
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
