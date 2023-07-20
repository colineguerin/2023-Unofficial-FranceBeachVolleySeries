<?php

namespace App\Controller;

use App\Form\FilterUserType;
use App\Form\SearchUserType;
use App\Repository\UserRepository;
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
    public function index(UserRepository $userRepository, Request $request, PaginatorInterface $paginator): Response
    {
        // Update player total points
        $users = $userRepository->findAll();
        foreach ($users as $user) {
            $results = $user->getResults();
            $points = [];
            foreach ($results as $result) {
                $points[] = $result->getPoints();
            }

            $user->setPoint(array_sum($points));

            $userRepository->save($user, true);
        }

        // Get first three players
        $womanPodium = $userRepository->findWomanPodium();
        $manPodium = $userRepository->findManPodium();

        // Search bar
        $searchForm = $this->createForm(SearchUserType::class);
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
            'womanPodium' => $womanPodium,
            'manPodium' => $manPodium,
            'filterForm' => $filterForm,
        ]);
    }
}
