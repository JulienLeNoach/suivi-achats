<?php

namespace App\Controller;

use App\Form\ValidType;
use App\Form\ChangePasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChangePasswordController extends AbstractController
{
    /**
     * @Route("/utilisateurs/edit-password", name="edit_password")
     */
    public function editPassword(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ValidType::class, $user);

        $form->handleRequest($request);


        return $this->render('utilisateurs/edit_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
