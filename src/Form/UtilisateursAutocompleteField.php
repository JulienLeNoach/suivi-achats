<?php

namespace App\Form;

use App\Entity\Utilisateurs;
use Symfony\Component\Form\AbstractType;
use App\Repository\UtilisateursRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
class UtilisateursAutocompleteField extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'placeholder' => 'Sélectionnez un utilisateur', 
            'class' => Utilisateurs::class,
            'searchable_fields' => ['nom_utilisateur'],
            //'choice_label' => 'name',

            'query_builder' => function(UtilisateursRepository $utilisateursRepository) {
                $user = $this->security->getUser();
                return $utilisateursRepository->createQueryBuilder('u')->andWhere('u.code_service = :val')
                ->andWhere('u.etat_utilisateur = 1')
                ->setParameter('val', $user->getCodeService()->getId());
            },
            //'security' => 'ROLE_SOMETHING',
        ]);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}
