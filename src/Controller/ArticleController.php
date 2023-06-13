<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    #[Route('/article/{slug}', name: 'article.show')]
    public function show(Article $article, Request $request, EntityManagerInterface $manager): Response
    {

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setArticle($article);
            $comment->setUser($this->getUser());

            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('article.show', ['slug' => $article->getSlug()]);
        }

        return $this->render('article/show.html.twig', [
            'form' => $form,
            'article' => $article
        ]);
    }
}
