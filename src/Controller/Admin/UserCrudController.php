<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Utilisateurs')
            ->setEntityLabelInSingular('Utilisateur')

            ->setPageTitle('index', 'Blog V2 - Administration des utilisateurs')
            ->setPaginatorPageSize(10);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('fullName'),
            TextField::new('pseudo'),
            TextField::new('email')
                ->hideOnForm(),
            ChoiceField::new('roles')
                ->allowMultipleChoices()
                ->renderAsBadges([
                    'ROLE_ADMIN' => 'danger',
                    'ROLE_AUTHOR' => 'success',
                    'ROLE_USER' => 'info'
                ])
                ->setChoices([
                    'Utilisateur' => 'ROLE_USER',
                    'Auteur' => 'ROLE_AUTHOR',
                    'Administrateur' => 'ROLE_ADMIN'
                ]),
            DateTimeField::new('createdAt')
                ->hideOnForm()
                ->setFormTypeOption('disabled', 'disabled'),
        ];
    }
}
