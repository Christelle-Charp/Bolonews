<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('chapeau')
            ->add('contenu')
            ->add('image', FileType::class, [
                'label'=> 'Votre image',
                'mapped'=> 'false',
                'required'=> 'false',
                'constraints'=> [
                    new File([
                        'maxSize' => '3000k',
                        'mimeTypes'=> [
                            'image/*'
                        ],
                        'mimeTypesMessage'=> "Merci de télécharger l'illustration de votre article"
                    ])
                ]
            ])
            ->add('creation', null, [
                'widget' => 'single_text',
            ])
            ->add('modification')
            ->add('parution')
            ->add('auteur', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'id',
            ])
            ->add('likes', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
