<?php


namespace App\Form;

use App\Entity\Paris;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class ParisFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $match = $options['match']; // Récupérer le match passé en option

        $builder
            ->add('equipe', ChoiceType::class, [
                'choices' => [
                    $match->getHomeTeamName() => $match->getHomeTeamName() ,
                    $match->getAwayTeamName() => $match->getAwayTeamName(),
                ],
                'label' => 'Choisissez votre équipe',
                'expanded' => true,
                'multiple' => false,
                'attr' => ['class' => 'form-check'],
            ])
            ->add('mise', IntegerType::class, [
                'attr' => ['max' => $options['user_solde']],
                'label' => 'Mise (€)',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Paris::class,
            'match' => null, // Ajouter cette option pour passer le match
            'user_solde' => 0, // Option pour le solde utilisateur
        ]);
    }
}