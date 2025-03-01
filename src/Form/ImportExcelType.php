<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ImportExcelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('excel_file', FileType::class, [
            'label' => 'Importer de nouvelles données',
            'required' => true,
            'attr' => ['class' => 'form-control','id'=>'inputGroupFile02'], 
            'label_attr' => ['class' => 'mx-5','for'=>"inputGroupFile02"]
            
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
