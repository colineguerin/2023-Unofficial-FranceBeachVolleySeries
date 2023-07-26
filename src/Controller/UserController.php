<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\InactiveTeam;
use App\Service\PointsService;
use App\Service\RankingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
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
        InactiveTeam $inactiveTeam,
    ): Response
    {
        $currentUser = $security->getUser();

        if ($currentUser !== $user) {
            throw new AccessDeniedException('Vous n\'êtes pas autorisé à accéder à ce profil.');
        }

        $pointsService->updateUsersPoints();
        $inactiveTeam->updateInactiveTeams($user);
        $teams = $user->getTeams();
        $results = $user->getResults();

        if ($user->getAvatar() === null) {
            if ($user->getGender() == 0) {
                $user->setAvatar('https://api.dicebear.com/6.x/personas/svg?eyes=open&mouth=bigSmile,smile,smirk&hair=long&skinColor=e5a07e&hairColor=6c4545&clothingColor=f3b63a');
            } else {
                $user->setAvatar('https://api.dicebear.com/6.x/personas/svg?eyes=open&mouth=smirk&hair=shortCombover&skinColor=b16a5b&hairColor=362c47');
            }
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'teams' => $teams,
            'now' => new \DateTime(),
            'results' => $results,
            'ranking' => $rankingService->getRank($user),
        ]);
    }
}
