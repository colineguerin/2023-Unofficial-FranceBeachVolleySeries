<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\SearchAllType;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
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

        $searchForm = $this->createForm(SearchAllType::class);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            try {
                $permitNumber = $searchForm->get('search')->getData();
                $partner = $this->userRepository->findOneBy(['permitNumber' => $permitNumber]);

                if (!$partner) {
                    throw new \InvalidArgumentException('Ce numéro de licence n\'est pas attribué.');
                }

                if ($partner === $user) {
                    throw new \InvalidArgumentException('Vous ne pouvez pas choisir votre propre numéro de licence.');
                }

                $existingTeam = $this->teamRepository->findOneByPlayers($user, $partner);
                if ($existingTeam) {
                    throw new \InvalidArgumentException('Cette équipe existe déjà.');
                }

                $team = new Team();
                $team->addPlayer($user);
                $team->addPlayer($partner);
                $team->setCreatedAt(new \DateTimeImmutable());
                $team->setIsActive(true);

                $this->teamRepository->save($team, true);

                $this->addFlash('success', 'Nouvelle équipe créée !');

                return $this->redirectToRoute('app_user_show', ['id' => $userId], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('team/new.html.twig', [
            'searchForm' => $searchForm,
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
