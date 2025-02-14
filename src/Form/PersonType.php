<?php

namespace App\Form;

use App\Entity\Department;
use App\Entity\Person;
use App\Entity\Position;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('last_name')
            ->add('first_name')
            ->add('alert_new_request')
            ->add('alert_on_answer')
            ->add('alert_before_vacation')
            ->add('manager', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'choice_label' => 'id',
            ])
            ->add('position', EntityType::class, [
                'class' => Position::class,
                'choice_label' => 'id',
            ])
            ->add('submit',SubmitType::class) 
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
        ]);
    }
}
