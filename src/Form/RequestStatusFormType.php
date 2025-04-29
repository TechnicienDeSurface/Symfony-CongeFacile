<?php

namespace App\Form;

use App\Entity\RequestType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RequestStatusFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('answer', TextareaType::class, [
                'label' => 'Saisir un commentaire',
                'label_attr' => ['class' => 'form-label d-block flex flex-col gap-4'],
                'attr' => [
                    'class' => 'form-control bg-white border border-gray-300 rounded-md resize-y w-[55%] p-4 mt-5 mb-5',
                    'rows' => 5 // Augmente la hauteur du textarea
                ]
            ])
            ->add('accept', SubmitType::class, [
                'label' => 'Accepter la demande',
                'attr' => ['class' => 'btn btn-success mx-3 text-[#ebf1f4] bg-[#1A8245] rounded-lg w-[203px] h-[36px] font-medium my-7 cursor-pointer inline-block']
            ])
            ->add('refuse', SubmitType::class, [
                'label' => 'Refuser la demande',
                'attr' => ['class' => 'btn btn-danger btn-primary text-[#ebf1f4] bg-[#E10E0E] rounded-lg w-[210px] h-[36px] font-medium my-7 cursor-pointer inline-block']
            ]);
    }
}