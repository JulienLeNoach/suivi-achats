<?php

namespace App\Controller;

use DateTime;
use App\Entity\Achat;
use App\Factory\AchatFactory;
use App\Form\AchatType;
use App\Form\AddAchatType;
use App\Form\ValidAchatType;
use App\Repository\AchatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AchatController extends AbstractController
{

    private $entityManager;
    private $achatFactory;

    public function __construct(EntityManagerInterface $entityManager, AchatFactory $achatFactory)
    {
        $this->entityManager = $entityManager;
        $this->achatFactory = $achatFactory;
    }
    // La fonction addAchat() gère l'ajout d'un nouvel achat. Il crée un nouvel objet Achat,
    //  récupère l'utilisateur actuel, gère le formulaire de saisie, persiste
    //   et sauvegarde l'achat dans la base de données, affiche un message flash de succès,
    //    puis redirige vers la route 'ajout_achat' si le formulaire est soumis et valide. 
    //    Sinon, il rend la vue 'achat/addAchat.html.twig' avec le formulaire à afficher.
    #[Route('/achat/ajout_achat', name: 'ajout_achat')]
    public function addAchat(Request $request, EntityManagerInterface $entityManager): Response
    {
        $achat = $this->achatFactory->create();
        $form = $this->createForm(AddAchatType::class, $achat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $date = new DateTime('now', new \DateTimeZone('Europe/Paris'));
            $achat->setUtilisateurs($user);
            $achat->setDateSaisie($date);
            $achat->setEtatAchat(0);
            $this->entityManager->persist($achat);
            $this->entityManager->flush();


            $this->addFlash('success', 'Nouvel achat n° ' . $achat->getId() . "sauvegardé");
            return $this->redirectToRoute('ajout_achat');
        }
        return $this->render('achat/addAchat.html.twig', [
            'form' => $form->createView(),

        ]);
    }



}
