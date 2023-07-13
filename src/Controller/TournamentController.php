<?php

namespace App\Controller;

use App\Entity\Tournament;
use App\Form\RegisterTournamentType;
use App\Form\SearchTournamentType;
use App\Repository\TeamRepository;
use App\Repository\TournamentRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tournois')]
class TournamentController extends AbstractController
{
    #[Route('/', name: 'app_tournament_index', methods: ['GET', 'POST'])]
    public function index(Request $request, TournamentRepository $tournamentRepository, PaginatorInterface $paginator): Response
    {
        //search bar
        $form = $this->createForm(SearchTournamentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $allTournaments = $tournamentRepository->findByNameTypeOrLocation($search);
            $tournaments = $paginator->paginate(
                $allTournaments,
                $request->query->getInt('page', 1),
                10
            );
        } else {
            $allTournaments = $tournamentRepository->findBy([], ['tournamentDate' => 'DESC']);
            $tournaments = $paginator->paginate(
                $allTournaments,
                $request->query->getInt('page', 1),
                10
            );
        }

        //Get upcoming and past tournaments
        $allTournaments = $tournamentRepository->findAll();
        $currentDateTime = new DateTime();
        $pastTournaments = 0;
        $upcomingTournaments = 0;

        foreach ($allTournaments as $allTournament) {
            $tournamentDate = $allTournament->getTournamentDate();

            if ($tournamentDate < $currentDateTime) {
                $pastTournaments++;
            } else {
                $upcomingTournaments++;
            }
        }

        return $this->render('tournament/index.html.twig', [
            'tournaments' => $tournaments,
            'pastTournaments' => $pastTournaments,
            'upcomingTournaments' => $upcomingTournaments,
            'now' => $currentDateTime,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tournament_show', methods: ['GET'])]
    public function show(Tournament $tournament): Response
    {
        $teams = $tournament->getTeams();

        $registeredTeams = count($teams);
        $availableSpots = $tournament->getMaxTeam() - $registeredTeams;
        $completionPercentage = 100 - (($availableSpots / $tournament->getMaxTeam()) * 100);

        return $this->render('tournament/show.html.twig', [
            'tournament' => $tournament,
            'availableSpots' => $availableSpots,
            'completionPercentage' => $completionPercentage,
            'teams' => $teams,
        ]);
    }

    #[Route('/{id}/inscription', name: 'app_tournament_register', methods: ['GET', 'POST'])]
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
    }

}
