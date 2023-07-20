<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use App\Service\PointsService;
use App\Service\RankingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/joueur')]
class UserController extends AbstractController
{
    #[Route('/{id}/profil', name: 'app_user_show', methods: ['GET', 'POST'])]
    public function show(
        User $user,
        Security $security,
        RankingService $rankingService,
        PointsService $pointsService,
    ): Response
    {
        $currentUser = $security->getUser();

        if ($currentUser !== $user) {
            throw new AccessDeniedException('Vous n\'êtes pas autorisé à accéder à ce profil.');
        }

        $pointsService->updateUsersPoints();

        $teams = $user->getTeams();

        $results = $user->getResults();

        $currentDate = new \DateTime();

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'teams' => $teams,
            'now' => $currentDate,
            'results' => $results,
            'ranking' => $rankingService->getRank($user),
        ]);
    }
}
