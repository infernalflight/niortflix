<?php

namespace App\Form;

use App\Entity\Serie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class SerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la série',
                'required' => false,
            ])
            ->add('overview')
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'En cours' => 'returning',
                    'Abandonné' => 'canceled',
                    'Terminé' => 'ended',
                ],
                'placeholder' => '-- Choisir un statut --',
            ])
            ->add('genres',null, [
                'attr' => [
                ]
            ])
            ->add('vote')
            ->add('popularity')
            ->add('firstAirDate', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('lastAirDate')
            ->add('backdrop_file', FileType::class, [
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                            'image/avif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                        'maxSizeMessage' => 'Max file size 1 MB',
                    ])
                ]
            ])
            ->add('poster')
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn btn-primary',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Serie::class,
        ]);
    }
}
