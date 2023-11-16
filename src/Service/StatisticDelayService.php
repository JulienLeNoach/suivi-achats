<?php 

namespace App\Service;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatisticDelayService  extends AbstractController
{


    public function totalDelayPerMonth(array $achats): array
    {
        $transmission = [];
        $notification = [];
        $delaiTotal = [];
    
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
                $achat[$monthName] = $achat['Mois_' . $monthNumber] ?? 0;
                unset($achat['Mois_' . $monthNumber]);
            }
        }
        unset($achat); // Délier la dernière référence pour éviter des effets de bord
    
        // Calcul des sommes pour Transmission et Notification
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
    
        // Calculer la ligne "Délai TOTAL"
        foreach ($monthNames as $monthName) {
            $delaiTotal[$monthName] = $transmission[$monthName] + $notification[$monthName];
        }
        $delaiTotal['source'] = 'Délai TOTAL';
        $transmission['source'] = 'Transmission';
        $notification['source'] = 'Notification';
    
        // Organiser les éléments dans l'ordre spécifié
        $orderedAchats = [];
        foreach (['ANT GSBDD', 'BUDGET', 'APPRO', 'FIN', 'PFAF', 'Chorus formul.'] as $source) {
            foreach ($achats as $achat) {
                if ($achat['source'] === $source) {
                    $orderedAchats[] = $achat;
                    break;
                }
            }
        }
    
        // Ajouter les éléments calculés aux positions spécifiées
        array_splice($orderedAchats, 2, 0, [$transmission]);
        array_splice($orderedAchats, 5, 0, [$notification]);
        $orderedAchats[] = $delaiTotal;
    
        return $orderedAchats;
    }
    

    

}