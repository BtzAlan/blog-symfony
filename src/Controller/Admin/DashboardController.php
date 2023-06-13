<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Contact;
use App\Entity\Ingredient;
use App\Entity\Media;
use App\Entity\Option;
use App\Entity\Recipe;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin.index')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Blog V2 - Administration')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Accueil', 'fa fa-home', 'app_home');

        yield MenuItem::subMenu('Recettes', 'fas fa-bowl-food')->setSubItems([
            MenuItem::linkToCrud('Ingredients', 'fas fa-shop', Ingredient::class),
            MenuItem::linkToCrud('Ajouter un ingredients', 'fas fa-plus', Ingredient::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Recettes', 'fas fa-receipt', Recipe::class),
        ]);
        yield MenuItem::subMenu('Articles', 'fas fa-newspaper')->setSubItems([
            MenuItem::linkToCrud('Articles', 'fas fa-newspaper', Article::class),
            MenuItem::linkToCrud('Ajouter un article', 'fas fa-plus', Article::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Catégories', 'fas fa-list', Category::class),
        ]);

        yield MenuItem::linkToCrud('Médiathèque', 'fas fa-photo-video', Media::class);
        yield MenuItem::linkToCrud('Commentaires', 'fas fa-comment', Comment::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Contact', 'fas fa-envelope', Contact::class);

        yield MenuItem::subMenu('Réglages', 'fas fa-cog')->setSubItems([
            MenuItem::linkToCrud('Général', 'fas fa-cog', Option::class)
        ]);
    }
}
