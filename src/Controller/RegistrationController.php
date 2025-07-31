<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //On recupère l'image issu du formulaire
            $imageFile = $form->get('photo')->getData();

            //Si une image a bien été uploadé
            if($imageFile){
                //On donne un nom de fichier à l'image avec uniqid pour avoir un nom unique
                // et on ajoute l'extansion devinée avec la fonction guessExtension()
                $fileName = uniqid(). '.' . $imageFile->guessExtension();
                //On envoi le fichier dans le dossier prévu pour les images
                $imageFile->move(
                    $this->getParameter('images_directory').'user/',
                    $fileName);
                    
            };
            //On enregistre le nom du fichier dans l'entité produit
            $user->setPhoto($fileName);

            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_app');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
