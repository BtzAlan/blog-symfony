<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\SubmitButton;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class IngredientController extends AbstractController
{
    #[Route('/ingredient', name: 'app_ingredient', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $repository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $ingredients
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/ingredient/edit/{id}', name: 'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(Ingredient $ingredients, Request $request, EntityManagerInterface $manager): Response
    {

        if ($this->getUser() !== $ingredients->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cet ingrédient.');
        }

        $form = $this->createForm(IngredientType::class, $ingredients);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            /** @var SubmitButton $cancelBtn */
            $cancelBtn = $form->get('cancel');
            if ($cancelBtn->isClicked()) {
                return $this->redirectToRoute('app_ingredient');
            }

            if ($form->isValid()) {
                $ingredient = $form->getData();

                $manager->persist($ingredient);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Votre ingrédient a été modifié avec success!'
                );

                return $this->redirectToRoute('app_ingredient');
            }
        }

        return $this->render('ingredient/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/ingredient/creation', name: 'ingredient.new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $ingredients = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredients);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            /** @var SubmitButton $cancelBtn */
            $cancelBtn = $form->get('cancel');
            if ($cancelBtn->isClicked()) {
                return $this->redirectToRoute('app_ingredient');
            }

            if ($form->isValid()) {
                $ingredient = $form->getData();
                $ingredient->setUser($this->getUser());

                $manager->persist($ingredient);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Votre ingrédient a été créé avec success!'
                );

                return $this->redirectToRoute('app_ingredient');
            }
        }

        return $this->render('ingredient/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/ingredient/suppression/{id}', name: 'ingredient.delete', methods: ["GET", 'POST'])]
    public function delete(EntityManagerInterface $manager, Ingredient $ingredient): Response
    {

        if ($this->getUser() !== $ingredient->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer cet ingrédient.');
        }

        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre ingrédient a été supprimé avec success!'
        );

        return $this->redirectToRoute('app_ingredient');
    }
}
