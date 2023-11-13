<?php 

namespace App\Service;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatisticDelayService  extends AbstractController
{


    public function totalDelayPerMonth(array $achats): array
{
    $transmission = [];
    $notification = [];

    $monthNames = [
        1 => 'Janvier',
        2 => 'Février',
        3 => 'Mars',
        4 => 'Avril',
        5 => 'Mai',
        6 => 'Juin',
        7 => 'Juillet',
        8 => 'Aout',
        9 => 'Septembre',
        10 => 'Octobre',
        11 => 'Novembre',
        12 => 'Decembre',
    ];

    // Renommer les clés "Mois_x" en utilisant les noms des mois
    foreach ($achats as &$achat) {
        foreach ($monthNames as $monthNumber => $monthName) {
            $achat[$monthName] = $achat['Mois_' . $monthNumber];
            unset($achat['Mois_' . $monthNumber]);
        }
    }

    foreach ($monthNames as $monthName) {
        $sumTransmission = 0;
        $sumNotification = 0;

        foreach ($achats as $achat) {
            if ($achat['source'] === 'ANT GSBDD' || $achat['source'] === 'BUDGET') {
                $sumTransmission += $achat[$monthName];
            } elseif ($achat['source'] === 'APPRO' || $achat['source'] === 'FIN') {
                $sumNotification += $achat[$monthName];
            }
        }

        $transmission[$monthName] = $sumTransmission;
        $notification[$monthName] = $sumNotification;
    }

    $transmission['source'] = 'Transmission';
    $notification['source'] = 'Notification';

    // Insérer "Transmission" en 3ème position et "Notification" en 6ème position
    array_splice($achats, 2, 0, [$transmission]);
    array_splice($achats, 5, 0, [$notification]);

    // Calculer la ligne "Délai TOTAL"
    $delaiTotal = [];
    $delaiTotal['source'] = 'Délai TOTAL';

    foreach ($monthNames as $monthName) {
        $delaiTotal[$monthName] = $transmission[$monthName] + $notification[$monthName];
    }

    // Insérer "Délai TOTAL" en 14ème position
    array_splice($achats, 13, 0, [$delaiTotal]);

    return $achats;
}

}