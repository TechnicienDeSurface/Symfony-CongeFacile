<?php

namespace App\Form;

use App\Entity\RequestType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RequestTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
            'label' => 'Nom du type',
            'attr' => ['class' => 'form-control'],
        ]);

        if (!empty($options['isAdd'])) {
            $builder->add('ajouter', SubmitType::class, [
                'label' => 'Ajouter',
                'attr' => ['class' => 'btn btn-success'],
            ]);
        } else {
            $builder
                ->add('mettreAJour', SubmitType::class, [
                    'label' => 'Mettre à jour',
                    'attr' => ['class' => 'btn btn-primary'],
                ])
                ->add('supprimer', SubmitType::class, [
                    'label' => 'Supprimer',
                    'attr' => ['class' => 'btn btn-danger'],
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RequestType::class,
            'isAdd' => false,
        ]);
    }
}
