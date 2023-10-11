<?php namespace App\Factory;

use App\Entity\Utilisateurs;

class UserFactory
{
    public function create(): Utilisateurs
    {
        return new Utilisateurs();
    }
}