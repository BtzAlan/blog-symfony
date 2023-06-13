<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(RecipeRepository $recipeRepository, ArticleRepository $articleRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'recipes' => $recipeRepository->findPublicRecipe(4),
            'articles' => $articleRepository->findAll(),
            'categories' => $categoryRepository->findAll(),
        ]);
    }
}
