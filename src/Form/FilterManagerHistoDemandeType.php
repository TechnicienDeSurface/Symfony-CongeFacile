<?php

namespace App\Form;

use App\Entity\Department;
use App\Entity\Person;
use App\Entity\Request;
use App\Entity\RequestType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterManagerHistoDemandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('created_at', null, [
            //     'widget' => 'single_text',
            // ])
            ->add('start_at', null, [
                'widget' => 'single_text',
            ])
            ->add('end_at', null, [
                'widget' => 'single_text',
            ])
            // ->add('receipt_file')
            // ->add('comment')
            // ->add('answer_comment')
            ->add('answer')
            // ->add('answer_at', null, [
            //     'widget' => 'single_text',
            // ])
            ->add('request_type', EntityType::class, [
                'class' => RequestType::class,
                'choice_label' => 'id',
            ])
            ->add('collaborator', EntityType::class, [
                'class' => Person::class,
                'choice_label' => 'id',
            ])
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Request::class,
        ]);
    }
}
