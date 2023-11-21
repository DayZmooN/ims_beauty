<?php

namespace App\Controller\Admin;

use App\Entity\AboutUs;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

class AboutUsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AboutUs::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('Name'),
            TextField::new('quote'),
            TextEditorField::new('description'),
            ImageField::new('thumbnail')
            ->setBasePath('asset/media/banners/')
            ->setUploadDir('public/asset/media/banners'),
            TextField::new('email'),
            TextField::new('phone'),
            TextField::new('adress'),
            TextField::new('scheduleWeekdays'),
            TextField::new('scheduleSat'),
            TextField::new('scheduleSun'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        // Remove the 'new' and 'delete' actions
        $actions
            ->disable('new', 'delete', 'detail');

        return $actions;
    }
}
