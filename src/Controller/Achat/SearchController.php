<?php

namespace App\Controller\Achat;

use App\Entity\CPV;
use App\Entity\Achat;
use App\Form\ValidType;
use App\Form\AddAchatType;
use App\Form\EditAchatType;
use App\Factory\AchatFactory;
use App\Form\AchatSearchType;
use App\Repository\AchatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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



    return $this->render('search/result_achat.html.twig', [
        'result_achat' => $result_achat,

    ]);
}

#[Route('/ajout_achat', name: 'ajout_achat')]
public function add(Request $request,SessionInterface $session,): Response
{
    
    $achat = $this->achatFactory->create();

    $form = $this->createForm(AddAchatType::class, $achat);
    $form->handleRequest($request);
    $currentUrl = $session->get('current_url');



    if ($form->isSubmitted() && $form->isValid()) {


        $montantAchat = $achat->getMontantAchat();
    
        // Formater le montant avec deux chiffres après la virgule et ",00" si nécessaire
        $montantAchatFormatted = number_format($montantAchat, 2, '.', '');
        
        // Ajouter ",00" si le montant ne contient pas de décimales
        if (strpos($montantAchatFormatted, '.') === false) {
            $montantAchatFormatted .= ',00';
        }       
        $this->entityManager->getRepository(Achat::class)->add($achat);

        $this->addFlash('success', 'Nouvel achat n° ' . $achat->getNumeroAchat() . " ajouté \n\n Computation actuel du CPV  '". $achat->getCodeCpv()->getLibelleCpv() ."' : ".  $achat->getCodeCpv()->getMtCpvAuto()."€ \n\n Reliquat actuel du CPV  '". $achat->getCodeCpv()->getLibelleCpv() ."' : " . $achat->getCodeCpv()->getMtCpvAuto() - $achat->getMontantAchat(). "€");
        return $this->redirect("/search");


    
}
    return $this->render('achat/addAchat.html.twig', [
        'form' => $form->createView(),

    ]);
}
#[Route('/valid_achat/{id}', name: 'valid_achat')]
public function valid(Request $request,$id,SessionInterface $session): Response
{
    $result_achat = $this->entityManager->getRepository(Achat::class)->findOneById($id);
    $cpvId = $result_achat->getCodeCpv();
    $form = $this->createForm(ValidType::class, null, []);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        if ($form->get('Valider')->isClicked()) {

        $this->entityManager->getRepository(Achat::class)->valid($request, $id);
        $cpvSold = $this->entityManager->getRepository(CPV::class)->find($cpvId);
        
        $cpvSold->setMtCpvAuto($cpvSold->getMtCpvAuto() - $result_achat->getMontantAchat());
        $this->entityManager->flush();
        $this->entityManager->persist($cpvSold);
        $this->addFlash('valid', 'Achat n° ' . $result_achat->getNumeroAchat() . " validé \n\n Computation actuel du CPV  '". $result_achat->getCodeCpv()->getLibelleCpv() ."' : ".  $result_achat->getCodeCpv()->getMtCpvAuto()."€ \n\n Reliquat actuel du CPV  '". $result_achat->getCodeCpv()->getLibelleCpv() ."' : " . $result_achat->getCodeCpv()->getMtCpvAuto() - $result_achat->getMontantAchat(). "€");

        return $this->redirect("/search");
        }
}
    return $this->render('achat/valid_achat.html.twig', [
        'result_achat' => $result_achat,
        'form' => $form->createView(),

    ]);
}

#[Route('/annul_achat/{id}', name: 'annul_achat')]
public function cancel($id, Request $request,SessionInterface $session): Response
{
    $currentUrl = $session->get('current_url');
    $query = $this->entityManager->getRepository(Achat::class)->cancel($id);
    $result_achat = $this->entityManager->getRepository(Achat::class)->findOneById($id);

    $this->addFlash('success', 'Achat n° ' . $result_achat->getNumeroAchat() . " annulé");

    return $this->redirect($currentUrl);
}

#[Route('/reint_achat/{id}', name: 'reint_achat')]
public function reint($id, Request $request,SessionInterface $session): Response
{
    $currentUrl = $session->get('current_url');
    $query = $this->entityManager->getRepository(Achat::class)->reint($id);

            $this->addFlash('success', 'Achat n° ' . $query->getNumeroAchat() . " réintégré \n\n Computation actuel du CPV  '". $query->getCodeCpv()->getLibelleCpv() ."' : ".  $query->getCodeCpv()->getMtCpvAuto()."€ \n\n Reliquat actuel du CPV  '". $query->getCodeCpv()->getLibelleCpv() ."' : " . $query->getCodeCpv()->getMtCpvAuto() - $query->getMontantAchat(). "€");


    return $this->redirect($currentUrl);
}

#[Route('/edit_achat/{id}', name: 'edit_achat', methods: ['GET', 'POST'])]
public function edit(Request $request, $id, Achat $achat,  AchatRepository $achatRepository,SessionInterface $session): Response
{
    $currentUrl = $session->get('current_url');

    $form = $this->createForm(EditAchatType::class, $achat);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $achatRepository->edit($achat, true);

        $this->addFlash('success', 'Achat n° ' . $achat->getNumeroAchat() . " modifié \n\n Computation actuel du CPV  '". $achat->getCodeCpv()->getLibelleCpv() ."' : ".  $achat->getCodeCpv()->getMtCpvAuto()."€ \n\n Reliquat actuel du CPV  '". $achat->getCodeCpv()->getLibelleCpv() ."' : " . $achat->getCodeCpv()->getMtCpvAuto() - $achat->getMontantAchat(). "€");
        return $this->redirect($currentUrl);
    }
    return $this->render('achat/edit_achat_id.html.twig', [
        'achat' => $achat,
        'form' => $form->createView(),
    ]);


}
}