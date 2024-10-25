<?php

namespace App\Form;

use App\Entity\Formations;
use App\Repository\FormationsRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
class FormationsAutocompleteField extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'placeholder' => 'Sélectionnez une formation', 
            'class' => Formations::class,
            
            // Search only in code_formation and libelle_formation
            'searchable_fields' => ['code_formation', 'libelle_formation'],

            'query_builder' => function(FormationsRepository $formationsRepository) {
                $user = $this->security->getUser();
                return $formationsRepository->createQueryBuilder('u')
                    ->andWhere('u.code_service = :val')
                    ->andWhere('u.etat_formation = 1')
                    ->setParameter('val', $user->getCodeService()->getId());
            },
        ]);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}
