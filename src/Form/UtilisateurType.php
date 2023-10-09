<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('login', TextType::class,
                ["mapped" => true,
                    'constraints' => [
                        new Length(min: 4, max: 20, maxMessage: 'Login too long', minMessage: 'Login too short'),
                    ]
                ])
            ->add('plainPassword', PasswordType::class,
                ["mapped" => false,
                    'constraints' => [new NotBlank(),
                        new NotNull(),
                        new Length(min: 8, max: 30, maxMessage: 'password too long', minMessage: 'password too short'),
                        new Regex("#^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,30}$#", "password too easy")
                    ]
                ])
            ->add('adresseEmail', EmailType::class)
            ->add('fichierPhotoProfil', FileType::class,
                ["mapped" => false,
                    'constraints' => [
                        new File(maxSize: '10M', extensions: ['jpg', 'png'], maxSizeMessage: 'the file size is too big', extensionsMessage: 'the extension of this file is not supported')
                    ],
                    'required'=>false,
                    'attr' => [
                        'accept' => 'image/png, image/jpeg'
                         ]
                ]
            )
            ->add('inscription', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
