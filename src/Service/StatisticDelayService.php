<?php 

namespace App\Service;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatisticDelayService  extends AbstractController
{


    public function totalDelayPerMonth(array $achats): array
    {


        $transmission = [];
        $notification = [];
    
        for ($month = 1; $month <= 12; $month++) {
            $sumTransmission = 0;
            $sumNotification = 0;
    
            foreach ($achats as $achat) {
                if ($achat['source'] === 'ANT GSBDD' || $achat['source'] === 'BUDGET') {
                    $sumTransmission += $achat['Mois_' . $month];
                } elseif ($achat['source'] === 'APPRO' || $achat['source'] === 'FIN') {
                    $sumNotification += $achat['Mois_' . $month];
                }
            }
    
            $transmission['Mois_' . $month] = $sumTransmission;
            $notification['Mois_' . $month] = $sumNotification;
        }
    
        $transmission['source'] = 'Transmission';
        $notification['source'] = 'Notification';
    
        // Insérez "Transmission" en 3ème position et "Notification" en 6ème position
        array_splice($achats, 2, 0, [$transmission]);
        array_splice($achats, 5, 0, [$notification]);
    
        // Calcul de la ligne "Délai TOTAL"
        $delaiTotal = [];
        $delaiTotal['source'] = 'Délai TOTAL';
    
        for ($month = 1; $month <= 12; $month++) {
            $delaiTotal['Mois_' . $month] = $transmission['Mois_' . $month] + $notification['Mois_' . $month];
        }
    
        // Insérez "Délai TOTAL" en 14ème position
        array_splice($achats, 13, 0, [$delaiTotal]);

        return $achats;
    }

}