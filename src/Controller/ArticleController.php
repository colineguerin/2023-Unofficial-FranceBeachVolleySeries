<?php

namespace App\Controller;

use App\Form\SearchAllType;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_index', methods: ['GET', 'POST'])]
    public function index(ArticleRepository $articleRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $searchForm = $this->createForm(SearchAllType::class);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $search = $searchForm->getData()['search'];
            $allArticles = $articleRepository->findByTitle($search);
        } else {
            $allArticles = $articleRepository->findBy([], ['createdAt' => 'DESC']);
        }

        $articles = $paginator->paginate(
            $allArticles,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render(
            'article/index.html.twig',
            [
                'articles' => $articles,
                'searchForm' => $searchForm
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
