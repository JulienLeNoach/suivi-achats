<?php

namespace App\Controller\Autocomplete;

use App\Repository\CPVRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CPVAutocompleteController extends AbstractController
{
    #[Route('/autocomplete/cpv', name: 'cpv_autocomplete', methods: ['GET'])]
    public function autocomplete(Request $request, CPVRepository $cpvRepository): JsonResponse
    {
        // Récupérer la requête (query) envoyée par le champ de saisie
        $query = $request->query->get('query', '');

        // Si la requête est vide, renvoyer un tableau vide
        if (empty($query)) {
            return new JsonResponse([]);
        }

        // Limiter la requête à 10 résultats
        $results = $cpvRepository->createQueryBuilder('cpv')
            ->where('cpv.libelle_cpv LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        // Transformer les résultats en un tableau de JSON
        $response = [];

        foreach ($results as $cpv) {
            $response[] = [
                'id' => $cpv->getId(),
                'text' => $cpv->getLibelleCpv(),
            ];
        }

        return new JsonResponse($response);
    }
}
