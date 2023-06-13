<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/utilisateur/edition/{id}', 'user.edit')]

    public function edit(User $choosenUser, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {

        if ($this->getUser() !== $choosenUser) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier ces informations.');
        }

        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }

        if ($this->getUser() !== $choosenUser) {
            return $this->redirectToRoute('recipe.index');
        }

        $form = $this->createForm(UserType::class, $choosenUser);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if ($hasher->isPasswordValid($choosenUser, $form->getData()->getPlainPassword())) {

                $user = $form->getData();
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Les informations de votre compte ont boen été modifiées'
                );

                return $this->redirectToRoute('recipe.index');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect'
                );
            }
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/utilisateur/edition-mot-de-passe/{id}', 'user.edit.password', methods: ['GET', 'POST'])]
    public function editPassword(User $choosenUser, Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $manager): Response
    {

        if ($this->getUser() !== $choosenUser) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier ces informations.');
        }

        $form = $this->createForm(UserPasswordType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if ($hasher->isPasswordValid($choosenUser, $form->getData()['plainPassword'])) {
                $choosenUser->setPlainPassword(
                    $form->getData()['newPassword']
                );

                $eventManager = $manager->getEventManager();
                $eventArgs = new LifecycleEventArgs($choosenUser, $manager);
                $eventManager->dispatchEvent(\Doctrine\ORM\Events::preUpdate, $eventArgs);

                $manager->persist($choosenUser);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Le mot depasse a été modifié'
                );

                return $this->redirectToRoute('recipe.index');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect'
                );
            }
        }

        return $this->render('user/edit_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
