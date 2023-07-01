<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findBy([], ['createdAt' => 'DESC']);

        $carouselArticles = $articleRepository->findBy([], ['createdAt' => 'DESC'], 3);

        return $this->render(
            'article/index.html.twig',
            [
                'articles' => $articles,
                'carouselArticles' => $carouselArticles
            ],
        );
    }

    #[Route('/article/{id}', name: 'article_show')]
    public function show(int $id, ArticleRepository $articleRepository):Response
    {
        $article = $articleRepository->findOneBy(['id' => $id]);

        if (!$article) {
            throw $this->createNotFoundException(
                'No article with id : '.$id.' found in article\'s table.'
            );
        }
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }
}
