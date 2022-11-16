<?php

namespace App\Form;

use App\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('icone' ,ChoiceType::class,
                [
                    'expanded'     => false,
                    'placeholder' => 'Choisir une icon',
                    'required'     => true,
                    // 'attr' => ['class' => '],
                    'multiple' => false,
                    //'choices_as_values' => true,

                    'choices'  => array_flip([
                        'dinner'        =>'dinner',
                        'bicycle'       => 'bicycle',
                        'shirt' => 'shirt',
                        'car' => 'car',
                        'construction' => 'construction',
                        'coffee-cup' => 'coffee-cup',
                    ]),
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
