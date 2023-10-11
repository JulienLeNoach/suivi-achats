<?php namespace App\Factory;

use App\Entity\Calendar;

class CalendarFactory
{
    public function create(): Calendar
    {
        return new Calendar();
    }
}