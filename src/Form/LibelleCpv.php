<?php

namespace App\Form;

use App\Entity\CPV;
use App\Repository\CPVRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
class LibelleCpv extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'placeholder' => 'Sélectionnez un CPV', 
            'class' => CPV::class,

            'query_builder' => function(CPVRepository $cPVRepository) {
                $user = $this->security->getUser();

                return $cPVRepository->createQueryBuilder('u')->andWhere('u.code_service = :val')
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
