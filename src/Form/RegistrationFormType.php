<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('nom', TextType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'Entrez votre nom'],
            ])
            ->add('prenom', TextType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'Entrez votre prénom'],
            ])
            ->add('dateNaissance', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'attr' => ['placeholder' => 'Sélectionnez votre date de naissance'],
                'constraints' => [
                    new Callback([$this, 'validateAge'])
                ],
            ])
            
            ->add('solde', IntegerType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'Solde initial'],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    public function validateAge($date, ExecutionContextInterface $context)
    {
        if ($date === null) {
            return;
        }

        $age = $date->diff(new \DateTime())->y;

        if ($age < 18) {
            $context->buildViolation('Vous devez avoir au moins 18 ans pour vous inscrire.')
                ->atPath('dateNaissance')
                ->addViolation();
        }
    }
}
