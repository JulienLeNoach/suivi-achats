<?php

namespace App\Form;

use App\Entity\Achat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AchatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_saisie')
            ->add('numero_achat')
            ->add('id_demande_achat')
            ->add('date_sillage')
            ->add('date_commande_chorus')
            ->add('date_valid_inter')
            ->add('date_validation')
            ->add('date_notification')
            ->add('date_annulation')
            ->add('numero_ej')
            ->add('objet_achat')
            ->add('type_marche')
            ->add('montant_achat')
            ->add('observations')
            ->add('etat_achat')
            ->add('place')
            ->add('devis')
            ->add('utilisateurs')
            ->add('code_cpv')
            ->add('num_siret')
            ->add('code_service')
            ->add('code_formation')
            ->add('code_uo')
            ->add('tva_ident')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Achat::class,
        ]);
    }
}
