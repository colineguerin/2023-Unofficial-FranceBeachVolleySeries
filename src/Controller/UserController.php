<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
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
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/{id}/profil', name: 'app_user_show', methods: ['GET', 'POST'])]
    public function show(User $user, TeamRepository $teamRepository, Security $security, UserRepository $userRepository): Response
    {
        $currentUser = $security->getUser();

        if ($currentUser !== $user) {
            throw new AccessDeniedException('Vous n\'êtes pas autorisé à accéder à ce profil.');
        }

        $teams = $user->getTeams();
        $results = $user->getResults();

        $currentDate = new \DateTime();

        // Get user's national rank compared to all users
        $users = $userRepository->findAll();
        $points = [];
        foreach ($users as $user) {
            $points[] = $user->getPoint();
        }
        $userPoint = $currentUser->getPoint();

        rsort($points);
        $index = array_search($userPoint, $points);
        if ($index !== false) {
            $ranking = $index + 1;
        } else {
            $ranking = 0;
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'teams' => $teams,
            'now' => $currentDate,
            'results' => $results,
            'ranking' => $ranking,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
