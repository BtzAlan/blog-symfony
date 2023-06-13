<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/recette', name: 'recipe.index', methods: ['GET'])]
    public function index(RecipeRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {

        $recipes = $paginator->paginate(
            $repository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/recette/publique', 'recipe.index.public', methods: ['GET'])]
    public function indexPublic(RecipeRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {

        $recipes = $paginator->paginate(
            $repository->findPublicRecipe(null),
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('recipe/index_public.html.twig', [
            'recipes' => $recipes
        ]);
    }

    #[Route('/recette/{id}', 'recipe.show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Recipe $recipe): Response
    {
        if (!$recipe->isIsPublic() && $this->getUser() !== $recipe->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à voir cette recette.');
        }

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route("/recette/creation", "recipe.new", methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {

        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            /** @var SubmitButton $cancelBtn */
            $cancelBtn = $form->get('cancel');
            if ($cancelBtn->isClicked()) {
                return $this->redirectToRoute('recipe.index');
            }

            if ($form->isValid()) {
                $ingredient = $form->getData();
                $ingredient->setUser($this->getUser());

                $manager->persist($ingredient);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Votre recette a été créé avec success!'
                );

                return $this->redirectToRoute('recipe.index');
            }
        }

        return $this->render('recipe/new.html.twig', [
            'form' => $form
        ]);
    }


    #[IsGranted('ROLE_USER')]
    #[Route('/recipe/edition/{id}', 'recipe.edit', methods: ['GET', 'POST'])]
    public function edit(recipe $recipes, Request $request, EntityManagerInterface $manager): Response
    {

        if ($this->getUser() !== $recipes->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cete recette.');
        }

        $form = $this->createForm(recipeType::class, $recipes);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            /** @var SubmitButton $cancelBtn */
            $cancelBtn = $form->get('cancel');
            if ($cancelBtn->isClicked()) {
                return $this->redirectToRoute('recipe.index');
            }

            if ($form->isValid()) {
                $recipe = $form->getData();

                $manager->persist($recipe);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Votre recette a été modifiée avec success!'
                );

                return $this->redirectToRoute('recipe.index');
            }
        }

        return $this->render('recipe/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/recipe/suppression/{id}', 'recipe.delete', methods: ["GET", 'POST'])]
    public function delete(EntityManagerInterface $manager, recipe $recipe): Response
    {

        if (!$recipe->isIsPublic() && $this->getUser() !== $recipe->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à voir cette recette.');
        }

        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre recette a été supprimée avec success!'
        );

        return $this->redirectToRoute('recipe.index');
    }
}
