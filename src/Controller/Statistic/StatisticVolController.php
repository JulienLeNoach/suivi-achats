<?php

namespace App\Controller\Statistic;

// ...

use App\Form\StatisticType;
use Dompdf\Dompdf;
use App\Form\ValidAchatType;
use App\Repository\AchatRepository;
use App\Service\StatisticVolValService;
use Symfony\UX\Chartjs\Model\Chart;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Achat; // Make sure this use statement is correct
use App\Service\CalendarService;

//La méthode showStat de la classe, qui est associée à la route '/statistic',
//est la fonction principale pour afficher les statistiques.
//D'abord, elle crée un formulaire à l'aide de la classe ValidEditAchatTyê
// et gère la requête HTTP entrante. Si le formulaire est soumis et validé,
// elle récupère la somme des achats pour deux types spécifiques
// (mpttaEtat et mabcEtat) via la méthode getCountsByDateAndType du dépôt AchatRepository.
//Ensuite, elle combine ces sommes par mois en un seul tableau, en incluant le total pour chaque mois.



class StatisticVolController extends AbstractController
{
    private $entityManager;
    private $achatRepository;
    private $projectDir;
    private $statisticService;
    public function __construct(EntityManagerInterface $entityManager, AchatRepository $achatRepository, KernelInterface $kernel, StatisticVolValService $statisticService)
    {
        $this->entityManager = $entityManager;
        $this->statisticService = $statisticService;
        $this->achatRepository = $achatRepository;
        $this->projectDir = $kernel->getProjectDir();
    }

    #[Route('/statistic/vol', name: 'app_statistic_vol')]
    public function showStat(Request $request, EntityManagerInterface $entityManager, ChartBuilderInterface $chartBuilder): Response
    {

        $form = $this->createForm(StatisticType::class, null, []);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mpttaEtat = 1;
            $mabcEtat = 0;
            $counts1 = $this->achatRepository->getPurchaseCountAndTotalAmount($mpttaEtat,$form);
            $counts1 = $this->statisticService->totalPerMonth($counts1);
            $counts2 = $this->achatRepository->getPurchaseCountAndTotalAmount($mabcEtat,$form);
            $counts2 = $this->statisticService->totalPerMonth($counts2);
            $purchaseCountByMonth = $this->statisticService->purchaseCountByMonth($counts1,$counts2);
            $purchaseTotalAmountByMonth = $this->statisticService->purchaseTotalAmountByMonth($counts1,$counts2);

            if ($form->get('recherche')->isClicked()) {


                return $this->render('statistic/index.html.twig', [
                    'form' => $form->createView(),
                    'counts1' => $counts1,
                    'counts2' => $counts2,
                    'purchaseCountByMonth' => $purchaseCountByMonth,
                    'purchaseTotalAmountByMonth' => $purchaseTotalAmountByMonth,
                ]);
            } 
                if ($form->get('graph')->isClicked() ) {


                $chartData = $this->statisticService->arrayMapChart( $counts1, $counts2, 'count');
                $chartData2 = $this->statisticService->arrayMapChart($counts1, $counts2, 'totalmontant');
                $datasets1 = $chartData['datasets'];
                $datasets2 = $chartData['datasets2'];
                $datasets3 = $chartData2['datasets'];
                $datasets4 = $chartData2['datasets2'];

                return $this->render('statistic/graphvolval.html.twig', [
                    'datasets1' => $datasets1,
                    'datasets2' => $datasets2,
                    'datasets3' => $datasets3,
                    'datasets4' => $datasets4,

                ]);
            } elseif ($form->get('excel')->isClicked()) {
                return $this->statisticService->generateExcel($counts1, $counts2);

            }
        }
        return $this->render('statistic/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
