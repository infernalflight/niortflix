<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Serie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class SerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => false,
            ])
            ->add('overview', TextareaType::class, [
                'attr' => [
                    'cols' => 6,
                    'rows' => 5,
                ],
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'En cours' => 'Returning',
                    'Terminé' => 'Ended',
                    'Abandonné' => 'Canceled',
                ],
                'placeholder' => '-- Choisissez un statut --',
                'expanded' => true,
            ])
            ->add('vote')
            ->add('popularity')
            ->add('genres')
            ->add('firstAirDate', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('lastAirDate', null, [
                'widget' => 'single_text',
            ])
            ->add('poster_file', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'maxSizeMessage' => 'Ton image est trop volumineuse..',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Ce format est pas ok',
                    ]),
                ]
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $formEvent) {
                $serie = $formEvent->getData();
                if ($serie && $serie->getPoster()) {
                    $form = $formEvent->getForm();
                    $form->add('delete_image', CheckboxType::class, [
                        'mapped' => false,
                        'required' => false,
                        'label' => 'Supprimer l\'image',
                    ]);
                }
            })
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => '-- Choisissez une catégorie --',
                'required' => false,
                'multiple' => true,
                'expanded' => true,
            ])
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
