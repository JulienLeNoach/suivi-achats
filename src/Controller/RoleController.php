<?php

namespace App\Controller;

use App\Form\RoleType;
use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

//Ce contrôleur gère les actions liées aux rôles des utilisateurs.
//La méthode "index" affiche un formulaire pour enregistrer un utilisateur
//avec son rôle, tandis que la méthode "getRole" renvoie le rôle d'un utilisateur
// spécifié. La méthode "saveRoles" met à jour les rôles d'un utilisateur en fonction
//des rôles sélectionnés dans une requête JSON.
class RoleController extends AbstractController
{


    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


#[IsGranted('ROLE_OPT_DROITS')]
    #[Route('/role', name: 'app_role')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(RoleType::class);
        $form->handleRequest($request);
        // if ($form->isSubmitted() && $form->isValid()) {
        //     $formData = $form->getData();
        //     dd($formData);
        //     $userId = $formData->getId(); // Remplacez cela par la manière dont vous obtenez l'ID de l'utilisateur
    
        //     // Chargez l'utilisateur existant depuis la base de données
        //     $utilisateur = $this->entityManager->getRepository(Utilisateurs::class)->find($userId);
    
        //     // Mettez à jour les propriétés de l'utilisateur existant avec les données du formulaire
        //     $utilisateur->setNomConnexion($formData->getNomConnexion());
        //     // Copiez d'autres propriétés de formulaire dans l'utilisateur existant
    
        //     $this->entityManager->flush();
    
        //     return new Response('Utilisateur mis à jour avec succès.', Response::HTTP_OK);
        // }
        return $this->render('role/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/get_role/{id_utilisateur}", name="get_role")
     */
    public function getRole($id_utilisateur)
    {
        $utilisateur = $this->entityManager->getRepository(Utilisateurs::class)
            ->find($id_utilisateur);
        return new JsonResponse(['role' => $utilisateur->getRoles()]);
    }


    /**
     * @Route("/save_roles2", name="save_roles2", methods={"POST"})
     */
    public function saveRoles(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $selectedValue = $data['selectedValue'];
        $selectedRoles = $data['selectedRoles'];

        $utilisateur = $this->entityManager
            ->getRepository(Utilisateurs::class)
            ->find($selectedValue);

        if (!$utilisateur) {
            return new Response('Utilisateur non trouvé.', Response::HTTP_NOT_FOUND);
        }

        $utilisateur->setRoles($selectedRoles);

        $this->entityManager->flush();

        return new Response('Rôles enregistrés avec succès.', Response::HTTP_OK);
    }
}
