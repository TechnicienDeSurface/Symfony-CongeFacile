<?php

namespace App\Form;

use App\Entity\Department;
use App\Entity\Person;
use App\Entity\Position;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PreferenceManagerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('alert_new_demand', CheckboxType::class, [
                'label'    => 'Être alerté par email lorsqu’une demande de congé est acceptée ou refusée',
                'required' => false,
                'attr' => ['class="sr-only peer relative cursor-point"']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer mes préférences', 
                'attr' => ['class'=>'w-full text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition"', 
                            'style' => 'background-color : #004C6C ; '], 
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
        ]);
    }
}
