<?php

namespace App\Controller;

use App\Entity\Tournament;
use App\Form\RegisterTournamentType;
use App\Form\SearchTournamentType;
use App\Repository\TournamentRepository;
use App\Repository\UserRepository;
use App\Service\CalculateTournament;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/tournois')]
class TournamentController extends AbstractController
{
    #[Route('/', name: 'app_tournament_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        TournamentRepository $tournamentRepository,
        PaginatorInterface $paginator,
        CalculateTournament $calculateTournament,
    ): Response
    {
        // Search bar
        $searchForm = $this->createForm(SearchTournamentType::class);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $search = $searchForm->getData()['search'];
            $allTournaments = $tournamentRepository->findByNameTypeOrLocation($search);

        } else {
            $allTournaments = $tournamentRepository->findBy([], ['tournamentDate' => 'DESC']);
        }

        // Pagination
        $tournaments = $paginator->paginate(
            $allTournaments,
            $request->query->getInt('page', 1),
            10
        );

        $currentDateTime = new DateTime();

        return $this->render('tournament/index.html.twig', [
            'tournaments' => $tournaments,
            'pastTournaments' => $calculateTournament->calculatePastTournaments($tournamentRepository->findAll()),
            'upcomingTournaments' => $calculateTournament->calculateUpcomingTournaments($tournamentRepository->findAll()),
            'now' => $currentDateTime,
            'searchForm' => $searchForm,
        ]);
    }

    #[Route('/{id}', name: 'app_tournament_show', methods: ['GET', 'POST'])]
    public function show(
        Tournament $tournament,
        Request $request,
        TournamentRepository $tournamentRepository,
        Security $security,
        UserRepository $userRepository
    ): Response
    {
        $teams = $tournament->getTeams();

        $registeredTeams = count($teams);
        $availableSpots = $tournament->getMaxTeam() - $registeredTeams;
        $completionPercentage = 100 - (($availableSpots / $tournament->getMaxTeam()) * 100);
        $currentDateTime = new DateTime();

        // Register a team
        $userId = $security->getUser()->getId();
        $user = $userRepository->findOneBy(['id' => $userId]);

        $form = $this->createForm(RegisterTournamentType::class, $tournament, ['current_user' => $user]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $teams = $tournament->getTeams();
            $teams[] = $form->get('teams')->getData()->first();
            foreach ($teams as $team)
            {
                $tournament->addTeam($team);
            }

            $tournamentRepository->save($tournament, true);
            $this->addFlash('success', 'Votre équipe a bien été inscrite.');

            return $this->redirectToRoute('app_user_show', ['id' => $userId], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tournament/show.html.twig', [
            'tournament' => $tournament,
            'availableSpots' => $availableSpots,
            'completionPercentage' => $completionPercentage,
            'teams' => $teams,
            'form' => $form,
            'now' => $currentDateTime,
        ]);
    }

    /* #[Route('/{id}/inscription', name: 'app_tournament_register', methods: ['GET', 'POST'])]
    public function registerTournament(Request $request, Tournament $tournament, TournamentRepository $tournamentRepository): Response
    {
        $form = $this->createForm(RegisterTournamentType::class, $tournament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $team = $tournament->getTeams()->first();
            $tournament->addTeam($team);
            $tournamentRepository->save($tournament, true);
            $this->addFlash('success', 'Votre équipe a bien été inscrite.');
            return $this->redirectToRoute('app_tournament_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tournament/register.html.twig', [
            'form' => $form,
            'tournament' => $tournament,
        ]);
    }*/

}
