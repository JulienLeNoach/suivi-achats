<?php

namespace App\Controller\Environnement;

use App\Entity\Utilisateurs;
use App\Form\UtilisateursType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UtilisateursRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/utilisateurs')]
#[IsGranted('ROLE_OPT_UTILISATEURS')]
class UtilisateursController extends AbstractController
{
    #[Route('/', name: 'app_utilisateurs_index', methods: ['GET'])]
    public function index(UtilisateursRepository $utilisateursRepository,Request $request, PaginatorInterface $paginator): Response
    {
        $searchTerm = $request->query->get('search', '');
        $perPage = $request->query->get('perPage', 5);
        $sortField = $request->query->get('sortField', 'id');
        $sortDirection = $request->query->get('sortDirection', 'asc');
    
        $queryBuilder = $utilisateursRepository->createQueryBuilder('utilisateurs');
    
        if (!empty($searchTerm)) {
            $queryBuilder->andWhere('utilisateurs.nom_utilisateur LIKE :searchTerm OR utilisateurs.trigram LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }
        $queryBuilder->andWhere("utilisateurs.etat_utilisateur = 1"); // Ajout du tri
        $queryBuilder->orderBy("utilisateurs.$sortField", $sortDirection); // Ajout du tri
    
        $query = $queryBuilder->getQuery();
    
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $perPage
        );
    
        return $this->render('utilisateurs/index.html.twig', [
            'pagination' => $pagination,
            'searchTerm' => $searchTerm,
            'perPage' => $perPage,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);
    }

    #[Route('/new', name: 'app_utilisateurs_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordEncoder): Response
    {
        $utilisateur = new Utilisateurs();
        $form = $this->createForm(UtilisateursType::class, $utilisateur);
        $form->handleRequest($request);
        // dd($utilisateur);
 
        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur->hashPassword($passwordEncoder);
            $this->processUserRoles($utilisateur, $form->get('isAdmin')->getData());
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            return $this->redirectToRoute('app_utilisateurs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('utilisateurs/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_utilisateurs_show', methods: ['GET'])]
    public function show(Utilisateurs $utilisateur): Response
    {
        return $this->render('utilisateurs/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }
    #[Route('/{id}/edit', name: 'app_utilisateurs_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Utilisateurs $utilisateur, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder): Response
    {
        $form = $this->createForm(UtilisateursType::class, $utilisateur);
        $form->handleRequest($request);
        $currentPassword = $utilisateur->getPassword();
        // dd($currentPassword);
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifiez si le champ de mot de passe est rempli
            $newPasswordData = $form->get('password')->getData();
            if ($newPasswordData !== null ) {
                // Générer un nouveau hachage pour le nouveau mot de passe
                $hashedPassword = $passwordEncoder->hashPassword($utilisateur, $newPasswordData);

                // dd($hashedPassword);
                $utilisateur->setPassword($hashedPassword);
            }else {
                // Si aucun nouveau mot de passe n'est fourni, restaurer le mot de passe actuel
                $utilisateur->setPassword($currentPassword);
            }
    
            $this->processUserRoles($utilisateur, $form->get('isAdmin')->getData());
            $entityManager->persist($utilisateur);

            $entityManager->flush();
            
            return $this->redirectToRoute('app_utilisateurs_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('utilisateurs/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }
    
    
    
    

    #[Route('/{id}', name: 'app_utilisateurs_delete', methods: ['POST'])]
    public function delete(Request $request, Utilisateurs $utilisateur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($utilisateur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_utilisateurs_index', [], Response::HTTP_SEE_OTHER);
    }

    private function processUserRoles(Utilisateurs $utilisateur, bool $isAdmin): void
    {
        if ($isAdmin) {
            $utilisateur->addRole('ROLE_ADMIN');
        } else {
            $utilisateur->removeRole('ROLE_ADMIN');
        }
    }
    
}
