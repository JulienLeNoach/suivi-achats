<?php

namespace App\Controller;

use App\Form\RegisterType;
use App\Entity\Utilisateurs;
use App\Factory\UserFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    private $entityManager;
    private $userFactory;

    public function __construct(EntityManagerInterface $entityManager, UserFactory $userFactory)
    {
        $this->entityManager = $entityManager;
        $this->userFactory = $userFactory;
    }

    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, UserPasswordHasherInterface $hash): Response
    {

        $user = $this->userFactory->create();
        $form = $this->createForm(RegisterType::class, $user);

        $form ->handleRequest($request);

        if($form->isSubmitted() && $form ->isValid()){

            $user = $form->getData();
            $password = $hash->hashPassword($user,$user->getPassword());
            $user->setPassword($password);
            $user->setRoles(["ROLES_USER"]);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', 'Bienvenu a ' . $user->getNomUtilisateur() . " nouvellement inscrit ");
            return $this->redirectToRoute('app_register');
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
