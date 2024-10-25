<?php
// src/Security/UserChecker.php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        // Vérifiez que l'utilisateur est bien une instance de votre entité utilisateur
        if (!$user instanceof \App\Entity\Utilisateurs) {
            return;
        }

        // Si l'utilisateur n'est pas actif (etat_utilisateur = 0), lever une exception
        if (!$user->getEtatUtilisateur()) {
            throw new CustomUserMessageAccountStatusException('Votre compte est inactif, veuillez contacter l\'administrateur.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Cette méthode peut rester vide ou contenir des vérifications supplémentaires après l'authentification
    }
}
