<?php

namespace App\Form;

use App\Entity\Fournisseurs;
use Symfony\Component\Form\AbstractType;
use App\Repository\FournisseursRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
class FournisseursAutocompleteField extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'placeholder' => 'Sélectionnez un fournisseur',
            'class' => Fournisseurs::class,

            // Only use the num_siret field for search
            'searchable_fields' => ['num_siret','nom_fournisseur'],

            'query_builder' => function (FournisseursRepository $fournisseursRepository) {
                $user = $this->security->getUser();
                // dd($this->security->getUser());

                return $fournisseursRepository->createQueryBuilder('f')
                    ->andWhere('f.code_service = :val')
                    ->andWhere('f.etat_fournisseur = 1')
                    ->setParameter('val', $user->getCodeService()->getId());
            },

            'max_results' => 100, // Adjust this as needed
        ]);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}
