<?php

namespace App\Form;

use App\Entity\CPV;
use App\Repository\CPVRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;
use App\Entity\Achat;

#[AsEntityAutocompleteField]
class LibelleCpv extends AbstractType
{
    private $security;
    private $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'placeholder' => 'Sélectionnez un CPV',
            'class' => CPV::class,
            'searchable_fields' => ['code_cpv', 'libelle_cpv'],
            'choice_label' => function (CPV $cpv, $key, $value) {
                $totalAchats = $this->getTotalAchatsForCPV($cpv);

                $label = $cpv->getLibelleCpv() . ' - Total achats : ' . $totalAchats . ' €';

                if ($totalAchats > $cpv->getMtCpvAuto()) {
                    $label .= ' - Deuxieme seuil atteint';
                } elseif ($totalAchats >= $cpv->getPremierSeuil()) {
                    $label .= ' - Premier seuil atteint';
                }

                return $label;
            },
            'query_builder' => function (CPVRepository $cPVRepository) {
                $user = $this->security->getUser();
                return $cPVRepository->createQueryBuilder('u')
                    ->andWhere('u.code_service = :val')
                    ->andWhere('u.etat_cpv = 1')
                    ->setParameter('val', $user->getCodeService()->getId());
            },
        ]);
    }

    private function getTotalAchatsForCPV(CPV $cpv)
    {
        $achatRepository = $this->entityManager->getRepository(Achat::class);
        return $achatRepository->getTotalAchatsForCPVByYear($cpv, date('Y'));
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}


