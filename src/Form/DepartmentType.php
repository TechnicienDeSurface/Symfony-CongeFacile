<?php

namespace App\Form;

use App\Entity\Department;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepartmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'label' => 'Nom du service',
            'attr' => ['class' => 'border rounded p-2 w-full']
        ])
        ->add('save', SubmitType::class, [
            'label' => 'Mettre à jour',
            'attr' => ['class' => 'bg-blue-600 text-white px-4 py-2 rounded']
        ])
        ->add('delete', SubmitType::class, [
            'label' => 'Supprimer',
            'attr' => ['class' => 'bg-red-600 text-white px-4 py-2 rounded']
        ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Department::class,
        ]);
    }
}
