<?php

namespace App\Controller;

use App\Form\SearchUserType;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResultController extends AbstractController
{
    #[Route('/classement', name: 'app_result', methods: ['GET', 'POST'])]
    public function index(UserRepository $userRepository, Request $request, PaginatorInterface $paginator): Response
    {
        /* Update player total points */
        $users = $userRepository->findAll();
        foreach ($users as $user)
        {
            $results = $user->getResults();
            $points = [];
            foreach ($results as $result)
            {
                $points[] = $result->getPoints();
            }

            $user->setPoint(array_sum($points));

            $userRepository->save($user, true);
        }

        /* Get first three players */
        $womanPodium = $userRepository->findWomanPodium();
        $manPodium = $userRepository->findManPodium();

        /* Search bar */
        $form = $this->createForm(SearchUserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $allPlayers = $userRepository->findByName($search);
            $players = $paginator->paginate(
                $allPlayers,
                $request->query->getInt('page', 1),
                10
            );
        } else {
            $allPlayers = $userRepository->findBy([], ['point' => 'DESC']);
            $players = $paginator->paginate(
                $allPlayers,
                $request->query->getInt('page', 1),
                10
            );
        }

        return $this->render('result/index.html.twig', [
            'players' => $players,
            'form' => $form,
            'womanPodium' => $womanPodium,
            'manPodium' => $manPodium
        ]);
    }
}
