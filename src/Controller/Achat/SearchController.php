<?php

namespace App\Controller\Achat;

use App\Entity\CPV;
use App\Entity\Achat;
use App\Form\ValidType;
use App\Form\AddAchatType;
use App\Form\EditAchatType;
use App\Factory\AchatFactory;
use App\Form\AchatSearchType;
use App\Repository\CPVRepository;
use App\Repository\AchatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\Statistic\VolVal\StatisticVolValService;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    private $entityManager;
    private $pagination;
    private $statisticService;
    private $achatFactory;

    public function __construct(EntityManagerInterface $entityManager,StatisticVolValService $statisticService,AchatFactory $achatFactory)
    {
        $this->entityManager = $entityManager;
        $this->statisticService = $statisticService;
        $this->achatFactory = $achatFactory;

    }

    // Cette fonction gère une requête de recherche avec un formulaire.
    // Elle récupère les données soumises dans le formulaire, construit
    // une requête de recherche en fonction de ces données, exécute la
    // requête et renvoie le résultat sous forme d'une vue Twig rendue
    // avec les données du formulaire et les résultats de la recherche.

    #[Route('/search', name: 'app_search')]
    public function index(Request $request,SessionInterface $session, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(AchatSearchType::class);
        $form->handleRequest($request);
    
        $perPage = $request->query->get('perPage', 5);

        if ($form->isSubmitted() && $form->isValid()) {

            $sortField = $request->query->get('sortField', 'date_saisie');
            $sortDirection = $request->query->get('sortDirection', 'desc');

            $currentUrl = $request->getUri(); // Récupérer l'URL actuelle
            $session->set('current_url', $currentUrl);
            // $session->set('form', $form->getData());
            $criteria = [
                // Les champs mappés à l'entité
                'mappedData' => $form->getData(),
                // Les champs non mappés
                'montant_achat_min' =>  $form["montant_achat_min"]->getData(),
                'date' => $form->get('date')->getData(),
                'zipcode' => $form->get('zipcode')->getData(),
                'debut_rec' => $form->get('debut_rec')->getData(),
                'fin_rec' => $form->get('fin_rec')->getData(),
                'all_user' => $form->get('all_user')->getData(),
                'tax' => $form->get('tax')->getData(),

                // Ajoutez autant de champs non mappés que nécessaire
            ];            
            $session->set('criteria', $criteria);

            $querybuilder = $this->entityManager->getRepository(Achat::class)->searchAchat($form);
            $querybuilder->orderBy("b.$sortField", $sortDirection);
            
            $query = $querybuilder->getQuery();

            $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                $perPage
            );

                $tax = $form["tax"]->getData(); // Récupérer la taxe si elle est définie

            // Récupérer les achats avec la limite et le décalage
            $achats = $pagination->getItems();
            
            if ($tax == "ttc") {
                foreach ($achats as $achat) {
                    $achat->setMontantAchat($achat->getMontantAchat() * ($achat->getTvaIdent()->getTvaTaux()/100) + $achat->getMontantAchat());
                }
            }
            // Gérer la réponse pour les requêtes AJAX
            // Réponse pour la recherche initiale
            return $this->render('search/index.html.twig', [
                'form' => $form->createView(),
                'achats' => $pagination,
                'tax' => $tax,
                'perPage' => $perPage,
                'sortField' => $sortField,
                'sortDirection' => $sortDirection
            ]);
        }
        // Réponse pour une requête GET initiale (sans soumission de formulaire)
        return $this->render('search/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
// 
#[Route('/result_achat/{id}', name: 'achat_result')]
public function show(Request $request,$id,SessionInterface $session): Response
{
    $result_achat = $this->entityManager->getRepository(Achat::class)->findOneById($id);
    $cpv = $result_achat->getCodeCpv();
    $cpvId = $cpv->getId();
    $cpvMt = $this->entityManager->getRepository(CPV::class)->getTotalMontantCPV($cpvId, $id);


    return $this->render('search/result_achat.html.twig', [
        'result_achat' => $result_achat,
        'cpvMt' => $cpvMt

    ]);
}
#[Route('/ajout_achat', name: 'ajout_achat')] //V2
public function add(Request $request, SessionInterface $session): Response
{
    $achat = $this->achatFactory->create();
    $form = $this->createForm(AddAchatType::class, $achat);
    $form->handleRequest($request);
    $currentUrl = $session->get('current_url');

    if ($form->isSubmitted() && $form->isValid()) {
        $cpv = $achat->getCodeCpv();
        $montantAchat = $achat->getMontantAchat();

        // Vérifier si l'ajout dépasse le montant restant dans le CPV
        if ($cpv->getMtCpvAuto() < $montantAchat) {
            $this->addFlash('error', 'L\'achat ne peut être ajouté car le montant du CPV disponible est insuffisant.');
        } else {
            // Mettre à jour le montant du CPV
            $cpv->setMtCpvAuto($cpv->getMtCpvAuto() - $montantAchat);
            $this->entityManager->persist($cpv);
            
            // Ajouter l'achat en base de données
            $this->entityManager->getRepository(Achat::class)->add($achat);

            $this->addFlash('valid', 'Achat n° ' . $achat->getNumeroAchat() . " ajouté avec succès. \n\n Montant restant du CPV '" . $cpv->getLibelleCpv() . "' : " . $cpv->getMtCpvAuto() . "€");

            return $this->redirect("/search");
        }
    }

    return $this->render('achat/addAchat.html.twig', [
        'form' => $form->createView(),
    ]);
}


#[Route('/valid_achat/{id}', name: 'valid_achat')] //V2
public function valid(Request $request, $id, SessionInterface $session): Response
{
    $result_achat = $this->entityManager->getRepository(Achat::class)->findOneById($id);
    $cpv = $result_achat->getCodeCpv();
    $cpvId = $cpv->getId();

    $form = $this->createForm(ValidType::class, null, []);
    $cpvMt = $this->entityManager->getRepository(CPV::class)->getTotalMontantCPV($cpvId, $id);
    
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->getRepository(Achat::class)->valid($request, $id);
        
            $this->entityManager->flush();
            $this->addFlash('valid', 'Achat n° ' . $result_achat->getNumeroAchat() . " validé \n\n Computation actuel du CPV '" . $cpv->getLibelleCpv() . "' : " . $cpvMt['computation'] . "€ \n\n Reliquat actuel du CPV '" . $cpv->getLibelleCpv() . "' : " . $cpv->getMtCpvAuto() . "€");

            return $this->redirect("/search");
        
    }

    return $this->render('achat/valid_achat.html.twig', [
        'result_achat' => $result_achat,
        'cpvMt' => $cpvMt,
        'form' => $form->createView(),
    ]);
}


#[Route('/annul_achat/{id}', name: 'annul_achat', methods: ['POST'])]
public function cancel($id, Request $request, SessionInterface $session, AchatRepository $achatRepository): JsonResponse
{
    $data = json_decode($request->getContent(), true);
    $comment = $data['comment'] ?? null;

    // Trouver l'achat correspondant à l'ID
    $achat = $this->entityManager->getRepository(Achat::class)->findOneById($id);
    if (!$achat) {
        return new JsonResponse(['success' => false, 'message' => 'Achat non trouvé'], 404);
    }

    // Récupérer le CPV associé à l'achat (mais on ne touche pas à mt_cpv_auto)
    $cpv = $achat->getCodeCpv();

    // Annuler l'achat (sans modifier mt_cpv_auto)
    $achat->setEtatAchat('1');  // Définir l'état de l'achat comme annulé
    $achat->setCommentaireAnnulation($comment);  // Ajouter le commentaire d'annulation
    $achat->setDateAnnulation(new \DateTime());  // Ajouter la date d'annulation

    // Sauvegarder les modifications en base de données
    $this->entityManager->flush();

    $this->addFlash('success', 'Achat n° ' . $achat->getNumeroAchat() . ' annulé avec succès.');

    // Redirection après annulation
    return new JsonResponse(['success' => true, 'redirectUrl' => $session->get('current_url')]);
}





#[Route('/reint_achat/{id}', name: 'reint_achat')] //V2
public function reint($id, Request $request, SessionInterface $session, AchatRepository $achatRepository, CPVRepository $cpvRepository): Response
{
    $currentUrl = $session->get('current_url');
    $achat = $this->entityManager->getRepository(Achat::class)->findOneById($id);
    $cpv = $achat->getCodeCpv();
    $montantAchat = $achat->getMontantAchat();

    // Récupérer le montant total des achats pour ce CPV sur l'année en cours
    $currentYear = (new \DateTime())->format('Y');
    $totalAchats = $achatRepository->getTotalAchatsForCPVByYear($cpv, $currentYear);

    // Obtenir le seuil autorisé pour le CPV
    $montant_cpv_auto = $cpv->getMtCpvAuto();

    // Calculer le nouveau total après réintégration
    $nouveau_total_cpv = $totalAchats + $montantAchat;

    // Vérifier si la réintégration dépasse le montant autorisé
    if ($nouveau_total_cpv > $montant_cpv_auto) {
        $this->addFlash('error', 'L\'achat ne peut être réintégré car le montant total des achats pour ce CPV dépasse le montant autorisé (' . $montant_cpv_auto . '€).');
    } else {
        // Mettre à jour le montant du CPV après réintégration
        $cpv->setMtCpvAuto($montant_cpv_auto - $montantAchat);
        $this->entityManager->persist($cpv);
        $this->entityManager->flush();

        // Réintégrer l'achat
        $this->entityManager->getRepository(Achat::class)->reint($id);
        $this->addFlash('success', 'Achat n° ' . $achat->getNumeroAchat() . " réintégré. Montant restant du CPV '" . $cpv->getLibelleCpv() . "' : " . $cpv->getMtCpvAuto() . "€.");
    }

    return $this->redirect($currentUrl);
}



#[Route('/edit_achat/{id}', name: 'edit_achat')] //V2
public function edit(Request $request, $id, Achat $achat, AchatRepository $achatRepository, CPVRepository $cpvRepository, SessionInterface $session): Response
{
    $currentUrl = $session->get('current_url');
    $form = $this->createForm(EditAchatType::class, $achat);
    $result_achat = $this->entityManager->getRepository(Achat::class)->findOneById($id);

    // Montant initial de l'achat
    $montant_achat_initial = $result_achat->getMontantAchat();

    // Récupérer le CPV lié à l'achat
    $cpv = $result_achat->getCodeCpv();
    $cpvId = $cpv->getId();

    // Calculer le montant total des achats pour ce CPV pour l'année en cours
    $currentYear = (new \DateTime())->format('Y');
    $totalAchats = $achatRepository->getTotalAchatsForCPVByYear($cpv, $currentYear);

    // Obtenir le seuil autorisé pour le CPV
    $montant_cpv_auto = $cpv->getMtCpvAuto();

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        // Récupérer le nouveau montant modifié de l'achat
        $montant_achat_modifie = $form->get('montant_achat')->getData();

        // Calculer le nouveau total d'achats si le montant est modifié
        $nouveau_total_cpv = $totalAchats - $montant_achat_initial + $montant_achat_modifie;

        // Vérifier si le montant total des achats dépasse le seuil autorisé
        if ($nouveau_total_cpv > $montant_cpv_auto) {
            $this->addFlash('error', 'Modification impossible : le total des achats pour ce CPV dépasse le montant autorisé (' . $montant_cpv_auto . ' €).');
        } else {
            // Sauvegarder les modifications de l'achat
            $achatRepository->edit($achat, true);

            // Sauvegarde de toutes les modifications
            $this->entityManager->flush();

            $this->addFlash('success', 'Achat n° ' . $achat->getNumeroAchat() . " modifié avec succès.");
            return $this->redirect($currentUrl);
        }
    }

    return $this->render('achat/edit_achat_id.html.twig', [
        'achat' => $achat,
        'form' => $form->createView(),
        'cpvMt' => [
            'totalAchats' => $totalAchats,
            'mt_cpv_auto' => $montant_cpv_auto
        ], // Passer les infos CPV à la vue
    ]);
}


}