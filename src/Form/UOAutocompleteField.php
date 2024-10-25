<?php

namespace App\Form;

use App\Entity\UO;
use App\Repository\UORepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
class UOAutocompleteField extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'placeholder' => 'Sélectionnez une unité organique', 
            'class' => UO::class,

            // Allow search on both code_uo and libelle_uo fields
            'searchable_fields' => ['code_uo', 'libelle_uo'],

            'query_builder' => function(UORepository $uORepository) {
                $user = $this->security->getUser();
                return $uORepository->createQueryBuilder('u')
                    ->andWhere('u.code_service = :val')
                    ->andWhere('u.etat_uo = 1')
                    ->setParameter('val', $user->getCodeService()->getId());
            },
        ]);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}
