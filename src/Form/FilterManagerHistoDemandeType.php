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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class FilterManagerHistoDemandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('created_at', null, [
            //     'widget' => 'single_text',
            // ])
            ->add('start_at',null, [
                'widget' => 'single_text',
            ])
            ->add('end_at', null, [
                'widget' => 'single_text',
            ])
            // ->add('receipt_file')
            // ->add('comment')
            // ->add('answer_comment')
            // ->add('answer')
            // ->add('answer_at', null, [
            //     'widget' => 'single_text',
            // ])
            ->add('request_type', EntityType::class, [
                'class' => RequestType::class,
                'choice_label' => 'name',
            ])
            ->add('collaborator', EntityType::class, [
                'class' => Person::class,
                'choice_label' => 'last_name',
            ])
            ->add('totalnbdemande', ChoiceType::class, [
                'label' => 'Nb demandes associées :',
                'required' => false,
                'choices' => [
                    'Croissant' => 'ASC',
                    'Décroissant' => 'DESC',
                ],
                'attr' => ['class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline'],
                'placeholder' => 'Choisir l\'ordre',
            ])
            // ->add('department', EntityType::class, [
            //     'class' => Department::class,
            //     'choice_label' => 'name',
            // ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => ['class' => 'bg-[#004c6c] hover:bg-blue-700 text-white font-bold rounded py-2 px-6'],
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
