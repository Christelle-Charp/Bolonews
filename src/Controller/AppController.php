<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
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

    #[Route('/search', name: 'app_search')]
    public function search(ArticleRepository $articleRepository, Request $request): Response
    {
        $articles = $articleRepository->findAll();
        return $this->render('app/search.html.twig', [
            'articles' => $articles,
        ]);
    }
}
