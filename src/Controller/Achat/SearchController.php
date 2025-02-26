<?php

namespace App\Controller\Achat;

use DateTime;
use App\Entity\CPV;
use App\Entity\Achat;
use App\Entity\Devis;
use App\Form\ValidType;
use App\Form\AddAchatType;
use App\Entity\JustifAchat;
use App\Form\EditAchatType;
use App\Factory\AchatFactory;
use App\Form\AchatSearchType;
use App\Repository\CPVRepository;
use App\Repository\AchatRepository;
use App\Service\AchatNumberService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Service\Statistic\VolVal\StatisticVolValService;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    private $entityManager;
    private $pagination;
    private $statisticService;
    private $achatFactory;
    private $achatNumberService;
    private $security;


    public function __construct(EntityManagerInterface $entityManager,Security $security,AchatNumberService $achatNumberService,StatisticVolValService $statisticService,AchatFactory $achatFactory)
    {
        $this->entityManager = $entityManager;
        $this->statisticService = $statisticService;
        $this->achatFactory = $achatFactory;
        $this->achatNumberService = $achatNumberService;
        $this->security = $security;

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
public function show($id): Response
{
    $result_achat = $this->entityManager->getRepository(Achat::class)->findOneById($id);
    $cpvMt = $this->entityManager->getRepository(CPV::class)->getTotalMontantCPV($result_achat->getCodeCpv()->getId(), $id);
    return $this->render('search/result_achat.html.twig', [
        'result_achat' => $result_achat,
        'cpvMt' => $cpvMt

    ]);
}
// Ajout de la route pour générer le fichier Excel à partir de la vue result_achat
#[Route('/generate_excel/{id}', name: 'generate_excel')]
public function generateFromResultAchat($id): Response
{
    // Récupérer l'achat par ID
    $result_achat = $this->entityManager->getRepository(Achat::class)->findOneById($id);

    if (!$result_achat) {
        $this->addFlash('error', 'Achat non trouvé.');
        return $this->redirectToRoute('achat_result', ['id' => $id]);
    }

    // Appeler la méthode pour générer le fichier Excel
    $dateValidation = $result_achat->getDateValidation();  // Si la date de validation est déjà présente
    $dateNotification = $result_achat->getDateNotification();  // Si la date de notification est déjà présente

    return $this->generateExcel($result_achat, $dateValidation, $dateNotification);
}
#[Route('/ajout_achat', name: 'ajout_achat')]
public function add(Request $request, SessionInterface $session): Response
{
    $achat = $this->achatFactory->create();
    $form = $this->createForm(AddAchatType::class, $achat);
    $form->handleRequest($request);
    $currentUrl = $session->get('current_url');

    // Récupérer les justificatifs avec `etat_justif = 1` et `type_justif = 'inf2000'`
    $justifsInf2000 = $this->entityManager->getRepository(JustifAchat::class)->findBy([
        'etat_justif' => 1,
        'type_justif' => 'inf2000'
    ]);

    // Récupérer les justificatifs avec `type_justif = 'sup20000'`
    $justifsSup20000 = $this->entityManager->getRepository(JustifAchat::class)->findBy([
        'etat_justif' => 1,
        'type_justif' => 'sup20000'
    ]);

    if ($form->isSubmitted() && $form->isValid()) {
        $cpv = $achat->getCodeCpv();
        $user = $this->security->getUser();
        $date = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $achat->setUtilisateurs($user);
        $achat->setDateSaisie($date);
        $achat->setEtatAchat(0);
        $numeroAchat = $this->achatNumberService->generateAchatNumber();
        $achat->setNumeroAchat($numeroAchat);

        // Si le type de marché est 0, enregistre l'achat sans justification
        if ($achat->getTypeMarche() === "0") {

            $this->entityManager->persist($achat);
            $this->entityManager->flush();

            $this->addFlash('valid', 'Achat n° ' . $achat->getNumeroAchat() . " ajouté avec succès. Montant restant du CPV '" . $cpv->getLibelleCpv() . "' : " . $cpv->getMtCpvAuto() . "€");

            return $this->redirect("/search");
        }

        // Si le montant d'achat est supérieur à 20 000 €
        if ($achat->getMontantAchat() > 20000) {
            $justifNonConcurrenceId = $request->request->get('justif_non_concurrence');
            $customNonConcurrenceInput = $request->request->get('custom_justif_sup');
            $devisData = $request->request->all('devis');

            // Gestion des devis s'ils sont renseignés
            if (is_array($devisData) && !empty(array_filter($devisData, fn($devis) => !empty($devis['candidat']) && !empty($devis['montantht'])))) {
                foreach ($devisData as $devisInfo) {
                    if (isset($devisInfo['candidat']) && strlen($devisInfo['candidat']) > 150) {
                        $this->addFlash('error', 'Le nom du candidat ne doit pas dépasser 150 caractères.');
                        return $this->redirectToRoute('ajout_achat');
                    }
                    if (isset($devisInfo['obs']) && strlen($devisInfo['obs']) > 250) {
                        $this->addFlash('error', 'L\'observation ne doit pas dépasser 250 caractères.');
                        return $this->redirectToRoute('ajout_achat');
                    }

                    $devis = new Devis();
                    $devis->setNomCandidat($devisInfo['candidat'] ?? null);
                    $devis->setMontantHt($devisInfo['montantht'] ?? 0);
                    $devis->setObs($devisInfo['obs'] ?? null);
                    $devis->setAchat($achat);
                    $this->entityManager->persist($devis);
                }
            }

            // Gestion des justifications de non-concurrence
            if ($justifNonConcurrenceId !== null) {
                $justifAchat = $this->entityManager->getRepository(JustifAchat::class)->find($justifNonConcurrenceId);
                $achat->setJustifAchat($justifAchat);
            } elseif ($customNonConcurrenceInput) {
                if (strlen($customNonConcurrenceInput) > 250) {
                    $this->addFlash('error', 'La justification personnalisée ne doit pas dépasser 250 caractères.');
                    return $this->redirectToRoute('ajout_achat');
                }

                $newJustif = new JustifAchat();
                $newJustif->setLibelleJustif($customNonConcurrenceInput);
                $newJustif->setTypeJustif('sup20000');
                $newJustif->setEtatJustif(false);
                $this->entityManager->persist($newJustif);
                $achat->setJustifAchat($newJustif);
            }

        } else {
            // Gestion des montants inférieurs à 2 000 € (type marché = 1)
            $justifAchatId = $request->request->get('justif_id');
            $customJustif = $request->request->get('custom_justif');

            if ($justifAchatId && $justifAchatId !== "new") {
                $justifAchat = $this->entityManager->getRepository(JustifAchat::class)->find($justifAchatId);
                $achat->setJustifAchat($justifAchat);
            } elseif ($customJustif) {
                if (strlen($customJustif) > 250) {
                    $this->addFlash('error', 'La justification personnalisée ne doit pas dépasser 250 caractères.');
                    return $this->redirectToRoute('ajout_achat');
                }

                $newJustif = new JustifAchat();
                $newJustif->setLibelleJustif($customJustif);
                $newJustif->setTypeJustif('inf2000');
                $newJustif->setEtatJustif(false);
                $this->entityManager->persist($newJustif);
                $achat->setJustifAchat($newJustif);
            }
        }


        $this->entityManager->persist($achat);
        $this->entityManager->flush();
        $cpvMt = $this->entityManager->getRepository(CPV::class)->getTotalMontantCPV($cpv->getCodeCpv(), $achat->getId());
        // dd($cpvMt);
        $this->addFlash('valid', 'Achat n° ' . $achat->getNumeroAchat() . " ajouté avec succès. Montant restant du CPV '" . $cpv->getLibelleCpv() . "' : " . $cpvMt['reliquat'] . "€");

        return $this->redirect("/search");
    }

    return $this->render('achat/addAchat.html.twig', [
        'form' => $form->createView(),
        'justifs' => $justifsInf2000,
        'justifsSup20000' => $justifsSup20000,
        'currentUrl' => $currentUrl
    ]);
}


private function generateExcel(Achat $result_achat, ?\DateTime $dateValidation, ?\DateTime $dateNotification): StreamedResponse
{
    // Chemin vers le modèle Excel
    $templatePath = $this->getParameter('kernel.project_dir') . '/public/Pochette_Suivi_achats_NG.xlsx';
    
    // Charger le fichier Excel existant
    $spreadsheet = IOFactory::load($templatePath);
    // Calcul du montant TTC
    $tvaTaux = $result_achat->getTvaIdent()->getTvaTaux() ?? 0;
    $montantTtc = $result_achat->getMontantAchat() * (1 + $tvaTaux / 100);
    $cpv = $result_achat->getCodeCpv();
    $cpvId = $cpv->getId();
    $cpvMt = $this->entityManager->getRepository(CPV::class)->getTotalMontantCPV($cpvId,$result_achat->getId());
    // dd($cpvMt["computation"]);
    // Choix de la feuille en fonction des conditions
    if ($result_achat->getTypeMarche() === '1' && $montantTtc < 2000) {
        $sheet = $spreadsheet->setActiveSheetIndex(1);
    } elseif ($result_achat->getTypeMarche() === '1' && $montantTtc > 20000) {
        $sheet = $spreadsheet->setActiveSheetIndex(2);
    } else {
        $sheet = $spreadsheet->setActiveSheetIndex(0);
    }

    // Définir les valeurs dans les cellules
    $typeMarcheLabel = $result_achat->getTypeMarche() === '1' ? 'MPPA' : 'MABC';
    $sheet->setCellValue('D7', $typeMarcheLabel); // Type de marché
    $sheet->setCellValue('C4', $result_achat->getNumeroAchat()); // N° Chrono
    $sheet->setCellValue('I4', $result_achat->getIdDemandeAchat()); // ID Demande Achat
    $sheet->setCellValue('I7', $result_achat->getNumeroMarche()); // ID Demande Achat
    $sheet->setCellValue('O7', $result_achat->getNumeroEjMarche()); // ID Demande Achat
    $sheet->setCellValue('N4', $result_achat->getUtilisateurs()->getTrigram() ?? ''); // Trigram utilisateur
    $sheet->setCellValue('C9', $result_achat->getNumSiret()->getNomFournisseur() ?? ''); // Nom fournisseur
    $sheet->setCellValue('Q9', $result_achat->getNumSiret()->getPme() ? 'Oui' : 'Non'); // PME

    // Modifications demandées
    $sheet->setCellValue('E11', $result_achat->getGSBDD()->getLibelleGsbdd() ?? ''); // GSBDD
    $sheet->setCellValue('E13', $result_achat->getCodeUo()->getLibelleUo() ?? ''); // Libellé UO
    $sheet->setCellValue('C15', $result_achat->getObjetAchat()); // Objet de l'achat (décalé)
    $sheet->setCellValue('D17', $result_achat->getMontantAchat()); // Montant HT (décalé)
    $sheet->setCellValue('I17', $result_achat->getTvaIdent()->getTvaLib()); // TVA (décalé)
    $sheet->setCellValue('N17', $montantTtc); // Montant TTC (décalé)
    $sheet->setCellValue('E19', $result_achat->getObservations()); // Observations (décalé)
    $sheet->setCellValue('C21', $result_achat->getCodeCpv()->getCodeCpv() ?? ''); // Code CPV (décalé)
    $sheet->setCellValue('P21', $result_achat->getCodeUo()->getCodeUo() ?? ''); // Code UO (décalé)
    $sheet->setCellValue('F23', $result_achat->getDateCommandeChorus() ? $result_achat->getDateCommandeChorus()->format('d/m/Y') : ''); // Date Commande Chorus (décalé)
    $sheet->setCellValue('O23', $result_achat->getDateValidInter() ? $result_achat->getDateValidInter()->format('d/m/Y') : ''); // Date Validation Interne (décalé)
    $sheet->setCellValue('F25', $dateValidation ? $dateValidation->format('d/m/Y') : ''); // Date Validation (décalé)
    $sheet->setCellValue('O25', $dateNotification ? $dateNotification->format('d/m/Y') : ''); // Date Notification (décalé)

    // Conditions spécifiques pour TypeMarche = 1 et Montant TTC < 2000
    if ($result_achat->getTypeMarche() === '1' && $montantTtc < 2000) {
        $sheet->setCellValue('K21', $cpvMt["computation"] ?? ''); // Dernière computation connue (décalé)
        $justification = $result_achat->getJustifAchat() ? $result_achat->getJustifAchat()->getLibelleJustif() : 'aucune justification renseignée';
        $sheet->setCellValue('E29', $justification); // Justification d'achat (décalé)
    }

    // Conditions spécifiques pour TypeMarche = 1 et Montant TTC > 20000
    if ($result_achat->getTypeMarche() === '1' && $montantTtc > 20000) {
        $sheet->setCellValue('K21', $result_achat->getCodeCpv()->getMtCpvAuto() ?? ''); // Dernière computation connue (décalé)
        $justification = $result_achat->getJustifAchat() ? $result_achat->getJustifAchat()->getLibelleJustif() : 'aucune justification renseignée';
        $sheet->setCellValue('F35', $justification); // Justification d'achat dans F35 (décalé)

        // Renseigner les informations sur les devis
        $devisList = $result_achat->getDevis();
        foreach ($devisList as $index => $devis) {
            if ($index >= 3) break; // Limite à 3 devis
            $row = 31 + $index; // Lignes 31, 32, et 33 pour les devis
            $sheet->setCellValue("C$row", $devis->getNomCandidat() ?? '');
            $sheet->setCellValue("J$row", $devis->getMontantHt() ?? '');
            $sheet->setCellValue("M$row", $devis->getObs() ?? '');
        }
    }

    // Configurer le téléchargement du fichier
    $writer = new Xlsx($spreadsheet);
    $fileName = 'Achat_' . $result_achat->getNumeroAchat() . '.xlsx';

    return new StreamedResponse(function () use ($writer) {
        $writer->save('php://output');
    }, 200, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
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
            $dateValidation = $form->get('val')->getData();
            $dateNotification = $form->get('not')->getData();
            $result_achat->setDateValidation($dateValidation);
            $result_achat->setDateNotification($dateNotification);
            $this->entityManager->getRepository(Achat::class)->valid($request, $id);
            $this->entityManager->flush();

            $this->addFlash('valid', 'Achat n° ' . $result_achat->getNumeroAchat() . " validé et fichier excel généré.");

            // Générer et renvoyer le fichier Excel en téléchargement
            return $this->generateExcel($result_achat, $dateValidation, $dateNotification);
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
    // if ($nouveau_total_cpv > $montant_cpv_auto) {
    //     $this->addFlash('error', 'L\'achat ne peut être réintégré car le montant total des achats pour ce CPV dépasse le montant autorisé (' . $montant_cpv_auto . '€).');
    // } else {
        // Mettre à jour le montant du CPV après réintégration
        // $cpv->setMtCpvAuto($montant_cpv_auto - $montantAchat);
        // $this->entityManager->persist($cpv);
        // $this->entityManager->flush();

        // Réintégrer l'achat
        $this->entityManager->getRepository(Achat::class)->reint($id);
        $cpvMt = $this->entityManager->getRepository(CPV::class)->getTotalMontantCPV($cpv, $id);
        // dd($cpvMt['reliquat']);

        $this->addFlash('success', 'Achat n° ' . $achat->getNumeroAchat() . " réintégré. Montant restant du CPV '" . $cpv->getLibelleCpv() . "' : " . $cpvMt['reliquat'] . "€.");
    // }   

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
        // if ($nouveau_total_cpv > $montant_cpv_auto) {
        //     $this->addFlash('error', 'Modification impossible : le total des achats pour ce CPV dépasse le montant autorisé (' . $montant_cpv_auto . ' €).');
        // } else {
            // Sauvegarder les modifications de l'achat
            $achatRepository->edit($achat, true);

            // Sauvegarde de toutes les modifications
            $this->entityManager->flush();

            $this->addFlash('success', 'Achat n° ' . $achat->getNumeroAchat() . " modifié avec succès.");
            return $this->redirect($currentUrl);
        // }
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