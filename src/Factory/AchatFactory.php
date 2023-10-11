<?php namespace App\Factory;

use App\Entity\Achat;

class AchatFactory
{
    public function create(): Achat
    {
        return new Achat();
    }
}