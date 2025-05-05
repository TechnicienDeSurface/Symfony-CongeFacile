<?php 

namespace App\Form;

use App\Entity\Position;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class EditJobForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $deleteClass = 'block';
        if($options['submit_label'] == "Mettre à jour"){
            $deleteClass = 'hidden'; 
        }

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du poste',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ ne peut pas être vide.',
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => $options['submit_label'],
                'attr' => ['class' => 'btn btn-primary'],
            ])
            ->add('delete', SubmitType::class, [
                'label' => 'Supprimer',
                'attr' => [
                    'class' => 'btn btn-primary '.$deleteClass ,
                    'onclick' => "return confirm('Êtes-vous sûr de vouloir supprimer ?');",
                    'class' => $deleteClass,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Position::class,
            'submit_label' => 'Ajouter',     // valeur par défaut pour éviter l’erreur
        ]);
    }
}
