<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/article/add', name: 'article_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {

        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();
            $this->addFlash('success', "Votre article a bien était ajouté !");
            return $this->redirectToRoute('app_home');
        }

        return $this->render('article/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/article/viewAll', name: 'article_viewAll')]
    public function viewArticles(ArticleRepository $articleRepository, EntityManagerInterface $entityManager): Response
    {
        $articles = $articleRepository->findAll();

        return $this->render('article/viewAll.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/article/view/{id}', name: 'article_view')]
    public function viewArticle(Article $article, ArticleRepository $articleRepository):Response
    {

        return $this->render('article/view.html.twig', [
           'article' => $article,
        ]);
    }
}
