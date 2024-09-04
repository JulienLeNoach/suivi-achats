<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class DelaySettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('delai_transmissions', IntegerType::class, [
                'label' => 'Délai pour Transmissions (jours)',
                'required' => true,
                'data' => 5, // Valeur par défaut
            ])
            ->add('delai_traitement', IntegerType::class, [
                'label' => 'Délai pour Traitement (jours)',
                'required' => true,
                'data' => 3, // Valeur par défaut
            ])
            ->add('delai_notifications', IntegerType::class, [
                'label' => 'Délai pour Notifications (jours)',
                'required' => true,
                'data' => 5, // Valeur par défaut
            ])
            ->add('delai_total', IntegerType::class, [
                'label' => 'Délai Total (jours)',
                'required' => true,
                'data' => 15, // Valeur par défaut
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
