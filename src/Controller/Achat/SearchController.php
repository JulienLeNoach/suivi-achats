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


        $cpvId = $achat->getCodeCpv()->getId();
        // Formater le montant avec deux chiffres après la virgule et ",00" si nécessaire
        $montantAchatFormatted = number_format($achat->getMontantAchat(), 2, '.', '');
        $cpvSold = $this->entityManager->getRepository(CPV::class)->find($cpvId);
        // $cpvMt = $this->entityManager->getRepository(CPV::class)->getTotalMontantCPVwithoutId($cpvId);

        $cpvSold->setMtCpvAuto($cpvSold->getMtCpvAuto() - $achat->getMontantAchat());
        // Ajouter ",00" si le montant ne contient pas de décimales
        if (strpos($montantAchatFormatted, '.') === false) {
            $montantAchatFormatted .= ',00';
        }       
        $this->entityManager->getRepository(Achat::class)->add($achat);

        $this->addFlash('valid', 'Achat n° ' . $achat->getNumeroAchat() . " ajouté \n\n Computation actuel du CPV '" . $achat->getCodeCpv()->getLibelleCpv() . "' : " . 90000-$cpvSold->getMtCpvAuto() . "€ \n\n Reliquat actuel du CPV '" . $achat->getCodeCpv()->getLibelleCpv() . "' : " . $cpvSold->getMtCpvAuto() . "€");        return $this->redirect("/search");


    
}
    return $this->render('achat/addAchat.html.twig', [
        'form' => $form->createView(),

    ]);
}
#[Route('/valid_achat/{id}', name: 'valid_achat')]
public function valid(Request $request, $id, SessionInterface $session): Response
{
    $result_achat = $this->entityManager->getRepository(Achat::class)->findOneById($id);
    $cpv = $result_achat->getCodeCpv();
    $cpvId = $cpv->getId();

    $form = $this->createForm(ValidType::class, null, []);
    $cpvMt = $this->entityManager->getRepository(CPV::class)->getTotalMontantCPV($cpvId, $id);
    
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        if ($cpv->getMtCpvAuto() <= 50000) {
            $this->addFlash('error', 'L\'achat ne peut être validé car le montant du CPV actuel est supérieur à 40 000€.');
        } else {
            if ($form->get('Valider')->isClicked()) {
                $this->entityManager->getRepository(Achat::class)->valid($request, $id);
            
                $this->entityManager->flush();
                $this->addFlash('valid', 'Achat n° ' . $result_achat->getNumeroAchat() . " validé \n\n Computation actuel du CPV '" . $cpv->getLibelleCpv() . "' : " . $cpvMt['computation'] . "€ \n\n Reliquat actuel du CPV '" . $cpv->getLibelleCpv() . "' : " . $cpv->getMtCpvAuto() . "€");

                return $this->redirect("/search");
            }
        }
    }

    return $this->render('achat/valid_achat.html.twig', [
        'result_achat' => $result_achat,
        'cpvMt' => $cpvMt,
        'form' => $form->createView(),
    ]);
}

#[Route('/annul_achat/{id}', name: 'annul_achat')]
public function cancel($id, Request $request,SessionInterface $session): Response
{
    $currentUrl = $session->get('current_url');
    $query = $this->entityManager->getRepository(Achat::class)->cancel($id);
    $result_achat = $this->entityManager->getRepository(Achat::class)->findOneById($id);
    $cpvId = $result_achat->getCodeCpv()->getId();
    $cpvSold = $this->entityManager->getRepository(CPV::class)->find($cpvId);
    $cpvSold->setMtCpvAuto($cpvSold->getMtCpvAuto() + $result_achat->getMontantAchat());
    $this->entityManager->persist($cpvSold);
    $this->entityManager->flush();
    $this->addFlash('success', 'Achat n° ' . $result_achat->getNumeroAchat() . " annulé.");

    return $this->redirect($currentUrl);
}

#[Route('/reint_achat/{id}', name: 'reint_achat')]
public function reint($id, Request $request,SessionInterface $session): Response
{
    $currentUrl = $session->get('current_url');
    $achat = $this->entityManager->getRepository(Achat::class)->findOneById($id);
    $cpvId = $achat->getCodeCpv()->getId();
    // dd($cpvId);

    $cpvSold = $this->entityManager->getRepository(CPV::class)->findOneByCodeCpv($cpvId);    // Formater le montant avec deux chiffres après la virgule et ",00" si nécessaire
    $montantAchatFormatted = number_format($achat->getMontantAchat(), 2, '.', '');
    // dd($cpvSold);
    $cpvSold->setMtCpvAuto($cpvSold->getMtCpvAuto() - $achat->getMontantAchat());
    $this->entityManager->persist($cpvSold);
    $this->entityManager->flush();
    $query = $this->entityManager->getRepository(Achat::class)->reint($id);
    $result_achat = $this->entityManager->getRepository(Achat::class)->findOneById($id);
            $this->addFlash('success', 'Achat n° ' .  $result_achat->getNumeroAchat() . " réintégré.");


    return $this->redirect($currentUrl);
}

#[Route('/edit_achat/{id}', name: 'edit_achat')]
public function edit(Request $request, $id, Achat $achat,  AchatRepository $achatRepository,SessionInterface $session): Response
{
    $currentUrl = $session->get('current_url');

    $form = $this->createForm(EditAchatType::class, $achat);

    $form->handleRequest($request);
    // dd($achat);

    if ($form->isSubmitted() && $form->isValid()) {
        $achatRepository->edit($achat, true);
        $this->addFlash('success', 'Achat n° ' . $achat->getNumeroAchat() . " modifié \n\n Computation actuel du CPV  '". $achat->getCodeCpv()->getLibelleCpv() ."' : ".  90000-$achat->getCodeCpv()->getMtCpvAuto()."€ \n\n Reliquat actuel du CPV  '". $achat->getCodeCpv()->getLibelleCpv() ."' : " . $achat->getCodeCpv()->getMtCpvAuto() . "€");
        return $this->redirect($currentUrl);
    }
    return $this->render('achat/edit_achat_id.html.twig', [
        'achat' => $achat,
        'form' => $form->createView(),
    ]);


}
}