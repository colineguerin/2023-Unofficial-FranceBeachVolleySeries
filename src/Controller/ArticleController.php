<?php

namespace App\Controller;

use App\Form\SearchArticleType;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_index', methods: ['GET', 'POST'])]
    public function index(ArticleRepository $articleRepository, Request $request): Response
    {
        $form = $this->createForm(SearchArticleType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $articles = $articleRepository->findByTitle($search);
        } else {
            $articles = $articleRepository->findBy([], ['createdAt' => 'DESC']);
        }

        return $this->render(
            'article/index.html.twig',
            [
                'articles' => $articles,
                'form' => $form
            ],
        );
    }

    #[Route('/article/{id}', name: 'article_show')]
    public function show(int $id, ArticleRepository $articleRepository): Response
    {
        $article = $articleRepository->findOneBy(['id' => $id]);

        $latestArticles = $articleRepository->findLatest(3);

        if (!$article) {
            throw $this->createNotFoundException(
                'No article with id : ' . $id . ' found in article\'s table.'
            );
        }
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'latestArticles' => $latestArticles,
        ]);
    }
}
