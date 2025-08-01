<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ArticleController extends AbstractController
{
    
    
    #[Route('/article', name: 'article_index')]
    public function index(ArticleRepository $articleRepository, Request $request): Response
    {
        $user = $this->getUser();
        $articlesPublies = $articleRepository->listByPublicationStatus($user, true);
        $articlesNonPublies = $articleRepository->listByPublicationStatus($user, false);
        return $this->render('article/index.html.twig', [
            'articlesPublies' => $articlesPublies,
            'articlesNonPublies' => $articlesNonPublies,
        ]);
    }

    #[Route('/article/show/{id}', name: 'article_show')]
    public function show(Article $article): Response
    {
        //Afficher un article en l'appelant par son id
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/article/create', name: 'article_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        //J'instancie un article vide
        $article = new Article();

        //Je crée le formulaire qui a pour base Article:
        $form = $this->createForm(ArticleType::class, $article);

        //Je charge l'objet $article et l'objet $form avec les informations récupérées en post
        $form->handleRequest($request);
    
        //Je vérifie si le formulaire a bien été soumis et validé
        if($form->isSubmitted() && $form->isValid()){
            //On récupère l'image  issu du formulaire
            $imageFile = $form->get('image')->getData();

            //Si une image a bien été uploadé:
            if ($imageFile){
                //On donne un nom de fichier à l'image avec uniqid pour avoir un nom unique
                //et on ajoute l'extansion devinée avec la fonction guessExtension()
                $fileName = uniqid(). '.' . $imageFile->guessExtension();
                //On envoie le fichier dans le dossier prévu pour les images des articles
                $imageFile->move(
                    $this->getParameter('images_directory').'article/',
                    $fileName
                );
            };
            //On enregistre le nom du fichier dans l'entité Article
            $article->setImage($fileName);

            //On enregistre la date et l'heure de la creation:
            $article->setCreation(new \DateTimeImmutable());
            //On ajoute l'auteur
            $article->setAuteur($user);

            //Je mets la requete crée avec les infos du formulaire en file d'attente
            $em->persist($article);
            //Je mets à jour la BDD avec la requete qui est en attente
            
            $em->flush();

            //Je crée un message flash: "L'article a bien été crée"
            //type: succes, error, warning
            //Dans cette fonction, il faut mettre le type et ensuite le message
            $this->addFlash('succes', 'Votre article a bien été crée.');
            //Et je fais mon retour sur la page qui liste tous les articles
            return $this->redirectToRoute('article_index');
        }
        //Sinon, je réaffiche le formulaire de creation d'article
        return $this->render('article/create.html.twig', [
            'form' => $form,
        ]);

        
    }

    #[Route('/article/update/{id}', name: 'article_update')]
    public function update(Request $request, EntityManagerInterface $em, Article $article): Response
    {
        
        //Je crée le formulaire qui a pour base Article:
        $form = $this->createForm(ArticleType::class, $article);

        //Je charge l'objet $article et l'objet $form avec les informations récupérées en post
        $form->handleRequest($request);

        //Je vérifie si le formulaire a bien été soumis et validé
        if($form->isSubmitted() && $form->isValid()){
            //On récupère l'image  issu du formulaire
            $imageFile = $form->get('image')->getData();
            //Si le champs est bien rempli, je vérifie s'il y a déjà une image
            if($imageFile !== null){
                $oldFile = $article->getImage();
                $uploadDir = $this->getParameter('images_directory') . 'article/';

                //Si il y un ancien fichier, je le supprime
                if($oldFile && file_exists($uploadDir . $oldFile)){
                    unlink($uploadDir . $oldFile);
                }
                //J'enregistre la nouvelle image
                $fileName = uniqid(). '.' . $imageFile->guessExtension();
                //On envoi le fichier dans le dossier prévu pour les images
                $imageFile->move(
                    $this->getParameter('images_directory').'article/',
                    $fileName);

                //On enregistre le nom du fichier dans l'entité Article
                $article->setImage($fileName);
            }

            

            //On ajoute la date de modification:
            $article->setModification(new \DateTime());

            //Je mets la requete crée avec les infos du formulaire en file d'attente
            $em->persist($article);
            //Je mets à jour la BDD avec la requete qui est en attente
            $em->flush();

            //Je crée un message flash: "L'article a bien été mis à jour"
            //type: succes, error, warning
            //Dans cette fonction, il faut mettre le type et ensuite le message
            $this->addFlash('succes', 'Votre article a bien été mis à jour.');
            //Et je fais mon retour sur la page qui liste tous les articles
            return $this->redirectToRoute('article_index');
        }
        //Sinon, je réaffiche le formulaire de creation d'article
        return $this->render('article/create.html.twig', [
            'form' => $form,
        ]);

        
    }
}
