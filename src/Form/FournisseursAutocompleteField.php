<?php

namespace App\Form;

use App\Entity\Fournisseurs;
use App\Repository\FournisseursRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
class FournisseursAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Fournisseurs::class,
            //'choice_label' => 'name',

            'query_builder' => function(FournisseursRepository $fournisseursRepository) {
                return $fournisseursRepository->createQueryBuilder('fournisseurs');
            },
            //'security' => 'ROLE_SOMETHING',
        ]);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}
