<?php

// src/Form/ResetPasswordRequestFormType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                    'attr' => [
                        'autocomplete' => 'email',
                        'class' => 'px-10 py-3 focus:bg-transparent w-full text-sm border outline-[#007bff] rounded transition-all',
                        'placeholder' => '****@mentalworks.fr',
                        ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer votre email',
                    ]),
                ],
            ])
            ->add('reset', SubmitType::class, [
                'label' => 'Demander à réinitialiser le mot de passe',
                'attr' => ['class' => 'px-6 py-2.5 w-full text-sm bg-[#004C6C] hover:bg-blue-600 text-white rounded active:bg-[#006bff]'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
