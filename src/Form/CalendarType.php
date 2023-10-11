<?php

namespace App\Form;

use App\Entity\Calendar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class CalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start', DateType::class, [
                'widget' => 'single_text',
                'label' =>'Jour '
            ])
            ->add('background_color', ChoiceType::class, [
                'label' =>'Type de journée ',
                'choices'  => [
                    ''=>'',
                    'Oeuvré' => "lightgray",
                    'Week-end' => "red",
                    'Férié' => "yellow",
                    'RTT' => "blue",
                    'Autre' => "green",
                ],
                ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
            'user_id' => null,
        ]);
        $resolver->setAllowedTypes('user_id', ['null', 'int', UserInterface::class]);
    }
}
