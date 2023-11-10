<?php

namespace App\Form;

use App\Entity\Appointements;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Services;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AvailabilityType extends AbstractType
{
    // public function buildForm(FormBuilderInterface $builder, array $options): void
    // {
    //     $builder
    //         ->add('DateTime', DateTimeType::class, [
    //             'date_label' => 'Date',
    //             'time_label' => 'Heure',
    //             'minutes' => range(0, 59, 15),
    //             'required' => true,
    //             'widget' => 'single_text',
    //             'attr' => [
    //                 'class' => 'datetimepicker', // Ajoutez une classe pour le JavaScript si nécessaire
    //             ],
    //             // Ajoutez des options pour limiter les heures
    //             'hours' => range(8, 17),
    //         ])
    //         ->add('DateTime', ChoiceType::class, [
    //             'choices' => $options['available_time_slots'],
    //             'label' => 'Sélectionnez un créneau',
    //         ])
    //         ->add('reserve', SubmitType::class, [
    //             'label' => 'Réserver'
    //         ]);
    // }




    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointements::class,
            'available_time_slots' => [],
        ]);
    }
    // src/Form/AvailabilityType.php

    // private function getAvailableTimeSlots()
    // {
    //     // Récupérez les créneaux disponibles depuis la base de données
    //     // ou définissez-les statiquement si vous ne gérez pas dynamiquement les créneaux pour l'instant
    //     return [
    //         '08:00 - 09:00' => '08:00',
    //         '09:00 - 10:00' => '09:00',
    //         '10:00 - 11:00' => '10:00',
    //         '11:00 - 12:00' => '11:00',
    //         // Ajoutez d'autres créneaux si nécessaire
    //     ];
    // }

    // private function isDateTimeAvailable($dateTime, EntityManagerInterface $entityManager)
    // {
    //     // Vérifier si un rendez-vous avec la même date et heure existe déjà
    //     $existingAppointment = $entityManager->getRepository(Appointements::class)->findOneBy(['DateTime' => $dateTime]);

    //     // Si aucun rendez-vous n'est trouvé pour cette date et heure, le créneau est disponible
    //     return $existingAppointment === null;
    // }
}
