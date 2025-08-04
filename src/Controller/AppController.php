<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\SearchType;
use App\Repository\ArticleRepository;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AppController extends AbstractController
{
    #[Route('/', name: 'app_app')]
    public function index(): Response
    {
        return $this->render('app/index.html.twig', [
            'controller_name' => 'AppController',
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

    #[Route('/show/{id}', name: 'article_show')]
    public function show(Article $article): Response
    {
        //Afficher un article en l'appelant par son id
        return $this->render('app/show.html.twig', [
            'article' => $article,
        ]);
    }
}
