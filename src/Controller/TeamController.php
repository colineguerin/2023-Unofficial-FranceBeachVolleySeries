<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\TeamType;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/équipe')]
class TeamController extends AbstractController
{
    private TeamRepository $teamRepository;
    private UserRepository $userRepository;

    public function __construct(TeamRepository $teamRepository, UserRepository $userRepository)
    {
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
    }

    #[Route('/créer', name: 'app_team_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Security $security): Response
    {
        $userId = $security->getUser()->getId();
        $user = $this->userRepository->findOneBy(['id' => $userId]);

        $team = new Team();

        $form = $this->createForm(TeamType::class, $team, ['current_user' => $user]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $createdAt = new \DateTimeImmutable('now');

                if ($team->getPlayers()->count() !== 1) {
                    $this->addFlash('danger', 'Veuillez sélectionner un seul partenaire.');
                    return $this->redirectToRoute('app_team_new', [], Response::HTTP_SEE_OTHER);
                }

                $partner = $team->getPlayers()->first();

                if (!$partner) {
                    throw new \InvalidArgumentException('Le partenaire sélectionné n\'existe pas.');
                }

                if ($partner == $user) {
                    throw new \InvalidArgumentException('Vous ne pouvez pas créer une équipe avec vous-même.');
                }

                $existingTeam = $this->teamRepository->findOneByPlayers($user, $partner);
                if ($existingTeam) {
                    $this->addFlash('danger', 'Cette équipe existe déjà.');
                    return $this->redirectToRoute('app_team_new', [], Response::HTTP_SEE_OTHER);
                }

                $team->addPlayer($user);
                $team->addPlayer($partner);
                $team->setCreatedAt($createdAt);
                $team->setIsActive(true);

                $this->teamRepository->save($team, true);

                $this->addFlash('success', 'Nouvelle équipe créée !');

                return $this->redirectToRoute('app_user_show', ['id' => $userId], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('team/new.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/reactivate', name: 'app_team_reactivate', methods: ['GET', 'POST'])]
    public function reactivate(Request $request, Team $team, Security $security): Response
    {
        if ($this->isCsrfTokenValid('reactivate' . $team->getId(), $request->request->get('_token'))) {
            $team->setIsActive(true);
            $team->setUpdatedAt(new \DateTimeImmutable());
            $this->teamRepository->save($team, true);
            $this->addFlash('success', 'Votre équipe a bien été réactivée.');
        }

        return $this->redirectToRoute('app_user_show', ['id' => $security->getUser()->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/delete', name: 'app_team_delete', methods: ['POST'])]
    public function delete(Request $request, Team $team, Security $security): Response
    {
        if ($this->isCsrfTokenValid('delete' . $team->getId(), $request->request->get('_token'))) {
            $this->teamRepository->remove($team, true);
            $this->addFlash('success', 'Votre équipe a bien été supprimée.');
        }

        return $this->redirectToRoute('app_user_show', ['id' => $security->getUser()->getId()], Response::HTTP_SEE_OTHER);
    }
}
