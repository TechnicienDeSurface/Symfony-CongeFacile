<?php

namespace App\Form;

use App\Entity\Department;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepartmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du service',
                'attr' => [
                    'class' => 'block min-w-0 grow py-1.5 pr-3 pl-1 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm/6',
                    'placeholder' => 'Nom du service',
                ],
            ])
            ->add('edit', SubmitType::class, [
                'label' => 'Mettre à jour',
                'attr' => [
                    'class' => 'text-[#ebf1f4] bg-[#004C6C] rounded-lg w-[154px] h-[35px] font-medium my-7 cursor-pointer inline-block',
                ],
            ])
            ->add('delete', SubmitType::class, [
                'label' => 'Supprimer',
                'attr' => [
                    'class' => 'text-[#ebf1f4] bg-[#E10E0E] rounded-lg w-[130px] h-[35px] font-medium my-7 cursor-pointer inline-block',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Department::class,
        ]);
    }
}
