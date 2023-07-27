<?php

namespace App\Service;

use App\Entity\Tournament;
use App\Repository\TeamRepository;
use App\Repository\TournamentRepository;
use Symfony\Component\Form\FormInterface;

class TournamentRegistration
{
    private TournamentRepository $tournamentRepository;
    private TeamRepository $teamRepository;

    public function __construct(TournamentRepository $tournamentRepository, TeamRepository $teamRepository)
    {
        $this->tournamentRepository = $tournamentRepository;
        $this->teamRepository = $teamRepository;
    }

    public function checkIfRegistered(Tournament $tournament, $userTeams): bool
    {
        $registeredTeams = $tournament->getTeams();

        $alreadyRegistered = false;

        foreach ($userTeams as $team) {
            if ($registeredTeams->contains($team)) {
                $alreadyRegistered = true;
            }
        }

        return $alreadyRegistered;
    }

    public function registerTeam(FormInterface $form, Tournament $tournament): void
    {
        $data = $form->getData()['team'];
        $team = $this->teamRepository->findOneBy(['id' => $data]);

        if (!$team) {
            throw new \InvalidArgumentException('Cette équipe n\'existe pas.');
        }
        if (!$team->isIsActive()) {
            throw new \InvalidArgumentException('L\'équipe que vous avez sélectionnée n\'est plus active. Vous devez d\'abord la réactiver sur votre profil.');
        }

        $registeredTeams = $tournament->getTeams();
        if ($registeredTeams->contains($team)) {
            throw new \InvalidArgumentException('Cette équipe est déjà inscrite.');
        }

        $tournament->addTeam($team);
        $this->tournamentRepository->save($tournament, true);
    }
}