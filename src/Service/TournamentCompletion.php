<?php

namespace App\Service;

use App\Entity\Tournament;

class TournamentCompletion
{
    public function getAvailableSpots(Tournament $tournament): int
    {
        $teams = $tournament->getTeams();
        $registeredTeams = count($teams);

        return $tournament->getMaxTeam() - $registeredTeams;
    }

    public function getCompletionPercentage(Tournament $tournament): float
    {
        $availableSpots = $this->getAvailableSpots($tournament);

        return 100 - (($availableSpots / $tournament->getMaxTeam()) * 100);
    }

}