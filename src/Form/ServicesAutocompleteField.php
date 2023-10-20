<?php

namespace App\Form;

use App\Entity\Services;
use App\Repository\ServicesRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
class ServicesAutocompleteField extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Services::class,
            'choice_label' => 'nom_service',

            'query_builder' => function(ServicesRepository $servicesRepository) {
                $user = $this->security->getUser();
                return $servicesRepository->createQueryBuilder('u')->andWhere('u.code_service = :val')
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
