<?php

namespace App\Controller;

use App\Entity\Tournament;
use App\Form\SearchTournamentType;
use App\Repository\TournamentRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tournois')]
class TournamentController extends AbstractController
{
    #[Route('/', name: 'app_tournament_index', methods: ['GET', 'POST'])]
    public function index(Request $request, TournamentRepository $tournamentRepository): Response
    {
        $form = $this->createForm(SearchTournamentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $tournaments = $tournamentRepository->findByNameTypeOrLocation($search);
        } else {
            $tournaments = $tournamentRepository->findBy([], ['tournamentDate' => 'ASC']);
        }

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
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tournament_show', methods: ['GET'])]
    public function show(Tournament $tournament): Response
    {
        return $this->render('tournament/show.html.twig', [
            'tournament' => $tournament,
        ]);
    }

}
