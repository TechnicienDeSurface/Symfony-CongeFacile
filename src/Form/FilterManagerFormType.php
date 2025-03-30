<?php 

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FilterManagerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('LastName', TextType::class, [
                'label' => 'Nom',
                'required' => false,
            ])
            ->add('FirstName', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
            ])
            ->add('Department', TextType::class, [
                'label' => 'Service',
                'required' => false,
            ])
            ->add('search', SubmitType::class, [
                'label' => 'Valider',
            ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null, // Vérifie que ce n'est pas lié à une entité
            'method' => 'POST',
        ]);
    }
}