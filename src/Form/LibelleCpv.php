<?php

namespace App\Form;

use App\Entity\CPV;
use App\Repository\CPVRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
class LibelleCpv extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => CPV::class,
            'choice_label' => 'libelle_cpv',

            'query_builder' => function(CPVRepository $cPVRepository) {
                return $cPVRepository->createQueryBuilder('cPV');
            },
            //'security' => 'ROLE_SOMETHING',
        ]);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}
