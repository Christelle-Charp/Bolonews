<?php

namespace App\Controller;

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
        $articles = $articleRepository->findAll();
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/article/create', name: 'article_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
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
}
