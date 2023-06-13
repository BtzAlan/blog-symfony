<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request, EntityManagerInterface $manager): Response
    {
        $contact = new Contact();

        if ($this->getUser()) {
            $contact->setFullName($this->getUser()->getFullName())
                ->setEmail($this->getUser()->getEmail());
        }

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            /** @var SubmitButton $cancelBtn */
            $cancelBtn = $form->get('cancel');
            if ($cancelBtn->isClicked()) {
                return $this->redirectToRoute('contact');
            }

            if ($form->isValid()) {
                $contact = $form->getData();

                $manager->persist($contact);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Votre demande de contact à bien été envoyée'
                );

                return $this->redirectToRoute('contact');
            }
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
