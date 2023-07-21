<?php

namespace App\Service;

use App\Entity\Team;
use App\Entity\User;

class InactiveTeam
{
    public function updateInactiveTeams(User $user): void
    {
        $teams = $user->getTeams();
        $now = new \DateTimeImmutable();

        foreach ($teams as $team) {
            $lastTournamentDate = $this->getLastTournamentDate($team);

            if ($lastTournamentDate) {
                $diff = $now->diff($lastTournamentDate);
                $monthsSinceLastTournament = $diff->y * 12 + $diff->m;

                if ($monthsSinceLastTournament >= 12) {
                    if ($team->getUpdatedAt()) {
                        $diff = $now->diff($team->getUpdatedAt());
                        $monthsSinceLastUpdate = $diff->y * 12 + $diff->m;
                        if ($monthsSinceLastUpdate >= 12) {
                            $team->setIsActive(false);
                        }
                    } else {
                        $team->setIsActive(false);
                    }
                }
            }
        }
    }

    private function getLastTournamentDate(Team $team): ?\DateTimeImmutable
    {
        $lastTournamentDate = null;
        $tournaments = $team->getTournaments();

        foreach ($tournaments as $tournament) {
            $tournamentDate = $tournament->getTournamentDate();

            if ($tournamentDate) {
                if (!$lastTournamentDate || $tournamentDate > $lastTournamentDate) {
                    $lastTournamentDate = $tournamentDate;
                }
            }
        }

        return $lastTournamentDate;
    }

}