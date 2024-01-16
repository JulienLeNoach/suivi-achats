<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/utilisateurs/edit-password", name="edit_password")
     */
    public function editPassword(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle password change logic
            // For example, use Symfony's security component to encode and update the password

            // Redirect to a success page or return a response
        }

        return $this->render('utilisateurs/edit_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
