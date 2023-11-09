<?php

namespace App\Form;

use App\Entity\Appointements;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Services;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AvailabilityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('DateTime', DateTimeType::class, [ // Update property name to DateTime
                'date_label' => 'Date',
                'time_label' => 'Heure',
                'minutes' => range(0, 59, 15),
                'required' => true,
                'widget' => 'single_text', // Specify how the date and time should be displayed
            ])
            ->add('services', EntityType::class, [
                'class' => Services::class, // Make sure the namespace and class name are correct
                'choice_label' => 'name',
            ])
            ->add('reserve', SubmitType::class, [
                'label' => 'Réserver'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointements::class,
        ]);
    }
}
