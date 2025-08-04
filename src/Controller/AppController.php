<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\SearchType;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\ArticleRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AppController extends AbstractController
{
    #[Route('/', name: 'app_app')]
    public function index(ArticleRepository $articleRepository): Response
    {
        //Je récupère l'article le plus commenté pour la une:
        $articleUne = $articleRepository->findArticlePlusCommente();
        //Je récupère les 4 articles paru le + récemment:
        $articles = $articleRepository->findQuatreArticles();
        return $this->render('app/index.html.twig', [
            'articleUne' => $articleUne,
            'articles' => $articles,
        ]);
    }

    #[Route('/search/{idCategorie}', name: 'app_search', defaults: ['idCategorie' => null])]
    public function search(ArticleRepository $articleRepository, CategorieRepository $categorieRepository, Request $request, ?int $idCategorie=null): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        //Je récupère un tableau avec toutes mes categories
        $categories = $categorieRepository->findAll();

        //J'initialise mon tableau avec tous les article
        $articles = $articleRepository->listAutorisee();

        //Je fais la recherche par catégorie 
        if($idCategorie){
            $categorie = $categorieRepository->find($idCategorie);
            if($categorie){
                $articles = $articleRepository->findByCategorie($categorie->getNom());
            }
            
        }

        if($form->isSubmitted() && $form->isValid()){
            $searchData = $form->get('query')->getData();
            //Je vérifie s'il y a des infos dans la recherche
            if(empty($searchData)){
                //Afficher tous les produits
                $articles = $articleRepository->listAutorisee();
            }else{
                //Afficher les produits correspondant à la recherche
                $articles = $articleRepository->findBySearch($searchData);
            }
        }

        return $this->render('app/search.html.twig', [
            'articles' => $articles,
            'categories' => $categories,
            'form' => $form->createView(),  // Nécessaire pour afficher le form de recherche
        ]);
    }

    #[Route('/show/{id}', name: 'app_show')]
    public function show(Article $article, Request $request, EntityManagerInterface $em): Response
    {
        //J'instancie un nouveau commenataire pour appeller le formulaire de commentaire
        $commentaire = new Commentaire(); 
        $commentaire->setArticle($article);
        $commentaire->setPublication(new \DateTimeImmutable());
        
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //Je rajoute l'utilisateur connecté qui est l'auteur
            $commentaire->setAuteur($this->getUser());
             //Je mets la requete crée avec les infos du formulaire en file d'attente
            $em->persist($commentaire);
            //Je mets à jour la BDD avec la requete qui est en attente
            $em->flush();
            //Et je fais mon retour sur la page de l'article avec tous les commentaires
            return $this->redirectToRoute('app_show', ['id' => $article->getId()]);

        }

        //Afficher un article en l'appelant par son id
        return $this->render('app/show.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
            'commentaires' => $article->getCommentaires(),

        ]);
    }
}
