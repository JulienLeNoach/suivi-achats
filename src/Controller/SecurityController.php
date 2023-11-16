<?php

namespace App\Controller;

use App\Entity\Fournisseurs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $entityManager;

    public function __construct(
        private RequestStack $requestStack,EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;

    }

    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
             return $this->redirectToRoute('app_account');
        }
        
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();


        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/deconnexion', name: 'app_logout')]
    public function logout(): RedirectResponse
    {
        $response = $this->redirectToRoute('app_login');

        // Ajouter un message flash pour afficher un message à l'utilisateur
        $this->addFlash('success', 'Vous avez été déconnecté.');
        return $this->redirectToRoute($response);


    }
}
