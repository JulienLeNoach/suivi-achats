<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AccountPasswordController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    // Cette fonction gère la modification du mot de passe d'un utilisateur connecté.
    // Elle utilise un formulaire de type ChangePasswordType pour récupérer
    // les données du formulaire soumis, vérifie si l'ancien mot de passe
    // saisi correspond à celui stocké en base de données à l'aide du service
    // UserPasswordHasherInterface, met à jour le mot de passe en base de données
    // et affiche un message de notification sur la page de modification de mot de passe.
    #[Route('/compte/password', name: 'app_account_password')]
    public function index(Request $request,UserPasswordHasherInterface $hash): Response
    {

        $notification = null;

        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);


        $form->handleRequest($request);

        if($form->isSubmitted() && $form ->isValid()){
            
            $user = $form->getData();
            $old_pwd = $form->get('old_password')->getData();

            if($hash->isPasswordValid($user, $old_pwd)){

                $new_pwd = $form->get('new_password')->getData();
                $password = $hash->hashPassword($user, $new_pwd);
                $user->setPassword($password);

                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $notification = 'Votre mot de passe à bien été modifié';

            }
            else{

                $notification = 'Les mots de passe ne correspondent pas';

            }

        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            'notification'=> $notification
        ]);
    }
}
