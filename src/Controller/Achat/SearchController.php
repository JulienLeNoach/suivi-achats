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


// #[Route('/ajout_achat', name: 'ajout_achat')] V1
// public function add(Request $request,SessionInterface $session,): Response
// {
    
//     $achat = $this->achatFactory->create();
//     $form = $this->createForm(AddAchatType::class, $achat);
//     $form->handleRequest($request);
//     $currentUrl = $session->get('current_url');

//     if ($form->isSubmitted() && $form->isValid()) {


//         $cpvId = $achat->getCodeCpv()->getId();
//         // Formater le montant avec deux chiffres après la virgule et ",00" si nécessaire
//         $montantAchatFormatted = number_format($achat->getMontantAchat(), 2, '.', '');
//         $cpvSold = $this->entityManager->getRepository(CPV::class)->find($cpvId);
//         // $cpvMt = $this->entityManager->getRepository(CPV::class)->getTotalMontantCPVwithoutId($cpvId);

//         $cpvSold->setMtCpvAuto($cpvSold->getMtCpvAuto() - $achat->getMontantAchat());
//         // Ajouter ",00" si le montant ne contient pas de décimales
//         if (strpos($montantAchatFormatted, '.') === false) {
//             $montantAchatFormatted .= ',00';
//         }
//         $this->entityManager->getRepository(Achat::class)->add($achat);

//         $this->addFlash('valid', 'Achat n° ' . $achat->getNumeroAchat() . " ajouté \n\n Computation actuel du CPV '" . $achat->getCodeCpv()->getLibelleCpv() . "' : " . 40000-$cpvSold->getMtCpvAuto() . "€ \n\n Reliquat actuel du CPV '" . $achat->getCodeCpv()->getLibelleCpv() . "' : " . $cpvSold->getMtCpvAuto() . "€");        return $this->redirect("/search");

// }
//     return $this->render('achat/addAchat.html.twig', [
//         'form' => $form->createView(),
//     ]);
// } 
#[Route('/valid_achat/{id}', name: 'valid_achat')] //V2
public function valid(Request $request, $id, SessionInterface $session): Response
{
    $result_achat = $this->entityManager->getRepository(Achat::class)->findOneById($id);
    // dd($result_achat);
    $cpv = $result_achat->getCodeCpv();
    $cpvId = $cpv->getId();
    $result_cpv = $this->entityManager->getRepository(CPV::class)->showCPVwithId($cpv);

    $form = $this->createForm(ValidType::class, null, []);
    $cpvMt = $this->entityManager->getRepository(CPV::class)->getTotalMontantCPV($cpvId, $id);
    
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        if ($form->get('Valider')->isClicked()) {
            $this->entityManager->getRepository(Achat::class)->valid($request, $id);
        
            $this->entityManager->flush();
            $this->addFlash('valid', 'Achat n° ' . $result_achat->getNumeroAchat() . " validé \n\n Computation actuel du CPV '" . $cpv->getLibelleCpv() . "' : " . $cpvMt['computation'] . "€ \n\n Reliquat actuel du CPV '" . $cpv->getLibelleCpv() . "' : " . $cpv->getMtCpvAuto() . "€");

            return $this->redirect("/search");
        }
    }

    return $this->render('achat/valid_achat.html.twig', [
        'result_achat' => $result_achat,
        'cpvMt' => $cpvMt,
        'form' => $form->createView(),
    ]);
}

// #[Route('/valid_achat/{id}', name: 'valid_achat')] V1
// public function valid(Request $request, $id, SessionInterface $session): Response
// {
//     $result_achat = $this->entityManager->getRepository(Achat::class)->findOneById($id);
//     $cpv = $result_achat->getCodeCpv();
//     $cpvId = $cpv->getId();
//     $result_cpv = $this->entityManager->getRepository(CPV::class)->showCPVwithId($cpv);

//     $form = $this->createForm(ValidType::class, null, []);
//     $cpvMt = $this->entityManager->getRepository(CPV::class)->getTotalMontantCPV($cpvId, $id);
    
//     $form->handleRequest($request);

//     if ($form->isSubmitted() && $form->isValid()) {

//         if ($result_cpv["somme_montants"] > 40000) {
//             $this->addFlash('error', 'L\'achat ne peut être validé car le montant du CPV actuel est supérieur à 40 000€.');
//         } else {
//             if ($form->get('Valider')->isClicked()) {
//                 $this->entityManager->getRepository(Achat::class)->valid($request, $id);
            
//                 $this->entityManager->flush();
//                 $this->addFlash('valid', 'Achat n° ' . $result_achat->getNumeroAchat() . " validé \n\n Computation actuel du CPV '" . $cpv->getLibelleCpv() . "' : " . $cpvMt['computation'] . "€ \n\n Reliquat actuel du CPV '" . $cpv->getLibelleCpv() . "' : " . $cpv->getMtCpvAuto() . "€");

//                 return $this->redirect("/search");
//             }
//         }
//     }

//     return $this->render('achat/valid_achat.html.twig', [
//         'result_achat' => $result_achat,
//         'cpvMt' => $cpvMt,
//         'form' => $form->createView(),
//     ]);
// }
#[Route('/annul_achat/{id}', name: 'annul_achat')] //V2
public function cancel($id, Request $request, SessionInterface $session): Response
{
    $currentUrl = $session->get('current_url');
    $achat = $this->entityManager->getRepository(Achat::class)->findOneById($id);
    $cpv = $achat->getCodeCpv();

    // Augmenter le montant du CPV avec le montant de l'achat annulé
    $cpv->setMtCpvAuto($cpv->getMtCpvAuto() + $achat->getMontantAchat());
    $this->entityManager->persist($cpv);

    // Annuler l'achat
    $this->entityManager->getRepository(Achat::class)->cancel($id);
    $this->entityManager->flush();

    $this->addFlash('success', 'Achat n° ' . $achat->getNumeroAchat() . " annulé. Montant restant du CPV '" . $cpv->getLibelleCpv() . "' : " . $cpv->getMtCpvAuto() . "€.");

    return $this->redirect($currentUrl);
}


// #[Route('/annul_achat/{id}', name: 'annul_achat')] V1
// public function cancel($id, Request $request,SessionInterface $session): Response
// {
//     $currentUrl = $session->get('current_url');
//     $query = $this->entityManager->getRepository(Achat::class)->cancel($id);
//     $result_achat = $this->entityManager->getRepository(Achat::class)->findOneById($id);
//     $cpvId = $result_achat->getCodeCpv()->getId();
//     $cpvSold = $this->entityManager->getRepository(CPV::class)->find($cpvId);
//     $cpvSold->setMtCpvAuto($cpvSold->getMtCpvAuto() + $result_achat->getMontantAchat());
//     $this->entityManager->persist($cpvSold);
//     $this->entityManager->flush();
//     $this->addFlash('success', 'Achat n° ' . $result_achat->getNumeroAchat() . " annulé.");

//     return $this->redirect($currentUrl);
// }
#[Route('/reint_achat/{id}', name: 'reint_achat')] //V2
public function reint($id, Request $request, SessionInterface $session): Response
{
    $currentUrl = $session->get('current_url');
    $achat = $this->entityManager->getRepository(Achat::class)->findOneById($id);
    $cpv = $achat->getCodeCpv();
    $montantAchat = $achat->getMontantAchat();

    // Vérifier si la réintégration ne rendrait pas le montant du CPV négatif
    if ($cpv->getMtCpvAuto() < $montantAchat) {
        $this->addFlash('error', 'L\'achat ne peut être réintégré car le montant du CPV disponible est insuffisant.');
    } else {
        // Mettre à jour le montant du CPV après réintégration
        $cpv->setMtCpvAuto($cpv->getMtCpvAuto() - $montantAchat);
        $this->entityManager->persist($cpv);
        $this->entityManager->flush();

        // Réintégrer l'achat
        $this->entityManager->getRepository(Achat::class)->reint($id);
        $this->addFlash('success', 'Achat n° ' . $achat->getNumeroAchat() . " réintégré. Montant restant du CPV '" . $cpv->getLibelleCpv() . "' : " . $cpv->getMtCpvAuto() . "€.");
    }

    return $this->redirect($currentUrl);
}

// #[Route('/reint_achat/{id}', name: 'reint_achat')] V1
// public function reint($id, Request $request,SessionInterface $session): Response
// {
//     $currentUrl = $session->get('current_url');
//     $achat = $this->entityManager->getRepository(Achat::class)->findOneById($id);
//     $cpvId = $achat->getCodeCpv()->getId();
//     // dd($cpvId);

//     $cpvSold = $this->entityManager->getRepository(CPV::class)->findOneByCodeCpv($cpvId);    // Formater le montant avec deux chiffres après la virgule et ",00" si nécessaire
//     $montantAchatFormatted = number_format($achat->getMontantAchat(), 2, '.', '');
//     $cpv = $achat->getCodeCpv();
//     $result_cpv = $this->entityManager->getRepository(CPV::class)->showCPVwithId($cpv);
//     $somme_montants_cpv = $result_cpv['somme_montants'];
//     $montant_achat_reint = $achat->getMontantAchat();
//     if ($somme_montants_cpv  + $montant_achat_reint > 40000) {
//         $this->addFlash('error', 'L\'achat ne peut être réintégré car le montant du CPV actuel ne peut excéder 40 000€.');
//         return $this->redirect($currentUrl); // Assurez-vous de retourner une réponse ici aussi
//     } else {
//     $cpvSold->setMtCpvAuto($cpvSold->getMtCpvAuto() - $achat->getMontantAchat());
//     $this->entityManager->persist($cpvSold);
//     $this->entityManager->flush();
//     $query = $this->entityManager->getRepository(Achat::class)->reint($id);
//     $result_achat = $this->entityManager->getRepository(Achat::class)->findOneById($id);
//             $this->addFlash('success', 'Achat n° ' .  $result_achat->getNumeroAchat() . " réintégré.");
//     return $this->redirect($currentUrl);
//     }
// }

// #[Route('/edit_achat/{id}', name: 'edit_achat')] V1
// public function edit(Request $request, $id, Achat $achat, AchatRepository $achatRepository, SessionInterface $session): Response
// {
//     $currentUrl = $session->get('current_url');
//     $form = $this->createForm(EditAchatType::class, $achat);
//     $result_achat = $this->entityManager->getRepository(Achat::class)->findOneById($id);
//     $montant_achat_initial = $result_achat->getMontantAchat(); 
//     $form->handleRequest($request);

//     $cpv = $result_achat->getCodeCpv();
//     $result_cpv = $this->entityManager->getRepository(CPV::class)->showCPVwithId($cpv);
//     // if ($form->isSubmitted() && !$form->isValid()) {
//     //     $errors = $form->getErrors(true);
//     //     foreach ($errors as $error) {
//     //         // Affiche les erreurs dans la console ou dans les logs
//     //         dump($error->getMessage());
//     //     }
//     //     // Ajoutez un dd() ici pour voir les erreurs dans le dump si nécessaire
//     //     dd($errors);
//     // }
//     if ($form->isSubmitted() && $form->isValid()) {
//         // $montant_achat_modifie = $form->get('montant_achat')->getData();
//         // $somme_montants_cpv = $result_cpv['somme_montants'];

//         // if ($somme_montants_cpv - $montant_achat_initial + $montant_achat_modifie > 40000) {
//         //     $this->addFlash('error', 'L\'achat ne peut être modifié car le montant du CPV actuel ne peut excéder 40 000€.');
//         // } else {
//             // Mise à jour de l'achat en base de données
//             $achatRepository->edit($achat, true);

//             // Recalcul des données du CPV après la mise à jour de l'achat
//             $result_cpv = $this->entityManager->getRepository(CPV::class)->showCPVwithId($cpv);
//             $somme_montants_cpv = $result_cpv['somme_montants']; // Recalcule la somme après mise à jour de l'achat

//             // Mise à jour du CPV
//             $cpv->setMtCpvAuto(40000 - $somme_montants_cpv);
//             $this->entityManager->persist($cpv);

//             // Sauvegarde de toutes les modifications
//             $this->entityManager->flush();

//             $this->addFlash('success', 'Achat n° ' . $achat->getNumeroAchat() . " modifié \n\n Computation actuel du CPV  '". $achat->getCodeCpv()->getLibelleCpv() ."' : ". $result_cpv["somme_montants"]."€ \n\n Reliquat actuel du CPV  '". $achat->getCodeCpv()->getLibelleCpv() ."' : " . $result_cpv["reliquat"] . "€");
            
//             return $this->redirect($currentUrl);
//         // }
//     }

//     return $this->render('achat/edit_achat_id.html.twig', [
//         'achat' => $achat,
//         'form' => $form->createView(),
//     ]);
// }
#[Route('/edit_achat/{id}', name: 'edit_achat')] //V2
    public function edit(Request $request, $id, Achat $achat, AchatRepository $achatRepository, SessionInterface $session): Response
    {
        $currentUrl = $session->get('current_url');
        $form = $this->createForm(EditAchatType::class, $achat);
        $result_achat = $this->entityManager->getRepository(Achat::class)->findOneById($id);
        $montant_achat_initial = $result_achat->getMontantAchat(); 
        $form->handleRequest($request);

        $cpv = $result_achat->getCodeCpv();
        $result_cpv = $this->entityManager->getRepository(CPV::class)->showCPVwithId($cpv);
        $somme_montants_cpv = $result_cpv['somme_montants'];

        // Récupérer l'année en cours
        $currentYear = (new \DateTime())->format('Y');

        // Vérifier si l'année de date_saisie est l'année en cours
        $dateSaisieYear = $result_achat->getDateSaisie()->format('Y');
        $updateMontantCPV = $dateSaisieYear === $currentYear;

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si l'année de date_saisie correspond à l'année en cours
            if ($updateMontantCPV) {
                $montant_achat_modifie = $form->get('montant_achat')->getData();

                // Vérifier si la modification du montant fait dépasser 40 000€
                if ($somme_montants_cpv - $montant_achat_initial + $montant_achat_modifie > 40000) {
                    $this->addFlash('error', 'L\'achat ne peut être modifié car le montant du CPV actuel ne peut excéder 40 000€.');
                } else {
                    // Mise à jour du CPV
                    $cpv->setMtCpvAuto(40000 - ($somme_montants_cpv - $montant_achat_initial + $montant_achat_modifie));
                    $this->entityManager->persist($cpv);
                }
            }

            // Mise à jour de l'achat en base de données
            $achatRepository->edit($achat, true);

            // Sauvegarde de toutes les modifications
            $this->entityManager->flush();

            $this->addFlash('success', 'Achat n° ' . $achat->getNumeroAchat() . " modifié avec succès.");

            return $this->redirect($currentUrl);
        }

        return $this->render('achat/edit_achat_id.html.twig', [
            'achat' => $achat,
            'form' => $form->createView(),
        ]);
    }
}