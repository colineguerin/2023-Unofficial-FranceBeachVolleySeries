<?php

namespace App\Controller;

use App\Form\FilterUserType;
use App\Form\SearchAllType;
use App\Repository\UserRepository;
use App\Service\UserPoints;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ResultController extends AbstractController
{
    #[Route('/classement', name: 'app_result', methods: ['GET', 'POST'])]
    public function index(
        UserRepository     $userRepository,
        Request            $request,
        PaginatorInterface $paginator,
        UserPoints         $userPoints,
    ): Response
    {
        // Update player total points
        $userPoints->updateUsersPoints();

        // Search and filter forms
        $searchForm = $this->createForm(SearchAllType::class);
        $searchForm->handleRequest($request);

        $filterForm = $this->createForm(FilterUserType::class);
        $filterForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $search = $searchForm->getData()['search'];
            $allPlayers = $userRepository->findByName($search);
        } elseif ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $gender = $filterForm->getData()['gender'];
            if ($gender) {
                $allPlayers = $userRepository->findAllMenByRank();
            } else {
                $allPlayers = $userRepository->findAllWomenByRank();
            }
        } else {
            $allPlayers = $userRepository->findBy([], ['point' => 'DESC']);
        }

        // Pagination
        $players = $paginator->paginate(
            $allPlayers,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('result/index.html.twig', [
            'players' => $players,
            'searchForm' => $searchForm,
            'womanPodium' => $userRepository->findWomanPodium(),
            'manPodium' => $userRepository->findManPodium(),
            'filterForm' => $filterForm,
        ]);
    }
}
