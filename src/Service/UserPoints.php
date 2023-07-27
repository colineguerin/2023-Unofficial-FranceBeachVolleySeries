<?php

namespace App\Service;

use App\Repository\UserRepository;

class UserPoints
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function updateUsersPoints(): void
    {
        $users = $this->userRepository->findAll();
        foreach ($users as $user) {
            $results = $user->getResults();
            $points = [];
            foreach ($results as $result) {
                $points[] = $result->getPoints();
            }

            $user->setPoint(array_sum($points));

            $this->userRepository->save($user, true);
        }
    }
}