<?php namespace App\Service;


use App\Entity\Calendar;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\CalendarRepository;

class CalendarService  extends AbstractController
{

    public function __construct()
    {


    }

    public function formatEventsForFullCalendar(array $calendarEvents): array
{
    return array_map(function (Calendar $calendarEvent) {
        return [
            'id' => $calendarEvent->getId(),
            'user_id' => $calendarEvent->getUserId(),
            'start' => $calendarEvent->getStart()->format('Y-m-d\TH:i:s'),
            'backgroundColor' => $calendarEvent->getBackgroundColor(),
        ];
    }, $calendarEvents);
}
}