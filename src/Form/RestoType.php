<?php

namespace App\Form;

use App\Entity\Resto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RestoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', FileType::class, [
                'mapped' => false,
                'data_class' => null,
                'required' => false,
                'attr'=>['class'=>'js-file-attach custom-file-btn-input']
            ])
            ->add('fichier', FileType::class, [
                'mapped' => false,
                'data_class' => null,
                'required' => false,
                'attr'=>['class'=>'js-file-attach custom-file-btn-input']
            ])
            ->add('titre')
            ->add('description')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Resto::class,
        ]);
    }
}
