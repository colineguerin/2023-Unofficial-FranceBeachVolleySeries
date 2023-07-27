<?php

namespace App\Controller;

use App\Entity\Tournament;
use App\Form\RegisterTournamentType;
use App\Form\SearchAllType;
use App\Repository\TournamentRepository;
use App\Service\TournamentCalculator;
use App\Service\TournamentRegistration;
use App\Service\TournamentCompletion;
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
        Request              $request,
        TournamentRepository $tournamentRepository,
        PaginatorInterface   $paginator,
        TournamentCalculator $tournamentCalculator,
    ): Response
    {
        // Search bar
        $searchForm = $this->createForm(SearchAllType::class);
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

        return $this->render('tournament/index.html.twig', [
            'tournaments' => $tournaments,
            'pastTournaments' => $tournamentCalculator->calculatePastTournaments($tournamentRepository->findAll()),
            'upcomingTournaments' => $tournamentCalculator->calculateUpcomingTournaments($tournamentRepository->findAll()),
            'now' => new DateTime(),
            'searchForm' => $searchForm,
        ]);
    }

    #[Route('/{id}', name: 'app_tournament_show', methods: ['GET', 'POST'])]
    public function show(
        Tournament             $tournament,
        Request                $request,
        Security               $security,
        TournamentCompletion   $tournamentCompletion,
        TournamentRegistration $tournamentRegistration,
    ): Response
    {
        $userTeams = $security->getUser()->getTeams();

        $form = $this->createForm(RegisterTournamentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $tournamentRegistration->registerTeam($form, $tournament);
                $this->addFlash('success', 'Votre équipe a bien été inscrite.');
            } catch(\Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('tournament/show.html.twig', [
            'tournament' => $tournament,
            'alreadyRegistered' => $tournamentRegistration->checkIfRegistered($tournament, $userTeams),
            'availableSpots' => $tournamentCompletion->getAvailableSpots($tournament),
            'completionPercentage' => $tournamentCompletion->getCompletionPercentage($tournament),
            'teams' => $tournament->getTeams(),
            'form' => $form,
            'now' => new DateTime(),
        ]);
    }
}
