<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class,[
                'label'=>'Titre de votre article'
            ])
            ->add('chapeau', TextType::class,[
                'label'=>'Accroche de votre article'
            ])
            ->add('contenu', TextType::class,[
                'label'=>'Contenu de votre article'
            ])
            ->add('image', FileType::class, [
                'label'=> 'Votre image',
                'mapped'=> false,
                'required'=> false,
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
            
            ->add('parution', CheckboxType::class, [
                'label'=> 'Publier cet article',
                'required'=> false,
            ])
            
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'label'=> 'Sélectionnez votre catégorie',
                'choice_label' => 'nom',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'csrf_protection' => true,
        ]);
    }
}
