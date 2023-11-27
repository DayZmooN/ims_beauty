<?php

namespace App\Controller\Admin;

use App\Entity\Promotions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class PromotionsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {

        return Promotions::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('services')
        ->setCrudController(ServicesCrudController::class)
        ->setFormTypeOptions([
            'by_reference' => false,
            'multiple' => true, // Allows selection of multiple services
        ]);
        // Other fields like Name, Description, etc.
        yield TextField::new('Name');
        yield TextField::new('Description');
        yield DateTimeField::new('StarDate');
        yield DateTimeField::new('EndDate');
        yield NumberField::new('Discount');
    }
}
