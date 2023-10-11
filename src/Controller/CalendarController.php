<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Form\CalendarType;
use App\Factory\CalendarFactory;
use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CalendarController extends AbstractController
{

    private $entityManager;
    private $calendarFactory;

    public function __construct(EntityManagerInterface $entityManager, CalendarFactory $calendarFactory)
    {
        $this->entityManager = $entityManager;
        $this->calendarFactory = $calendarFactory;
    }

    // Cette fonction récupére tous les événements de la table "Calendar", 
    // les transforme en un format adapté pour l'affichage dans FullCalendar,
    //  puis crée un nouvel objet de calendrier et un formulaire pour ajouter 
    // un nouvel événement. Si le formulaire est soumis et valide, il enregistre
    //  l'événement et redirige l'utilisateur vers la page "/calendar". Enfin, elle
    //  renvoie une vue Twig avec les événements encodés en JSON, le calendrier et le formulaire de création d'événement.
    #[Route('/calendar', name: 'app_calendar')]
    public function index(EntityManagerInterface $entityManager, CalendarRepository $calendarRepository,Request $request,Security $security): Response
    {
        $user = $security->getUser();
        // Récupération des événements de la table "Calendar"
        $calendarEvents = 
        $this->entityManager->getRepository(Calendar::class)
            ->findByExampleField($user);
        // Transformation des événements pour les adapter à FullCalendar
        $events = array_map(function (Calendar $calendarEvent) {
            return [
                'id' => $calendarEvent->getId(),
                'user_id' => $calendarEvent->getUserId(),
                'start' => $calendarEvent->getStart()->format('Y-m-d\TH:i:s'),
                'backgroundColor' => $calendarEvent->getBackgroundColor(),

            ];
        }, $calendarEvents);
        $calendar = $this->calendarFactory->create();
        $form = $this->createForm(CalendarType::class, $calendar, [
         ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $calendar->setUserId($user);
            $calendarRepository->save($calendar, true);

            return $this->redirectToRoute('app_calendar', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('calendar/index.html.twig', [
            'events' => json_encode($events),
            'calendar' => $calendar,
            'form' => $form->createView(),
        ]);
    }
}
