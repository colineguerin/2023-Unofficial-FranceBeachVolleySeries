<?php

namespace App\Service;

class CalculateTournament
{
    public function calculatePastTournaments($tournaments): int
    {
        $now = new \DateTimeImmutable();
        $pastTournaments = 0;

        foreach ($tournaments as $tournament) {
            $tournamentDate = $tournament->getTournamentDate();

            if ($tournamentDate <= $now) {
                $pastTournaments++;
            }
        }

        return $pastTournaments;
    }

    public function calculateUpcomingTournaments($tournaments): int
    {
        $now = new \DateTimeImmutable();
        $upcomingTournaments = 0;

        foreach ($tournaments as $tournament) {
            $tournamentDate = $tournament->getTournamentDate();

            if ($tournamentDate > $now) {
                $upcomingTournaments++;
            }
        }

        return $upcomingTournaments;
    }
}