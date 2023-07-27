<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;

class UserRanking
{
    private UserRepository $userRepository;
    private Security $security;

    public function __construct(UserRepository $userRepository, Security $security)
    {
        $this->userRepository = $userRepository;
        $this->security = $security;
    }
    public function getRank(User $user): int
    {
        $currentUser = $this->security->getUser();
        $users = $this->userRepository->findAll();
        $points = [];
        foreach ($users as $user) {
            $points[] = $user->getPoint();
        }
        $userPoint = $currentUser->getPoint();

        rsort($points);
        $index = array_search($userPoint, $points);
        if ($index !== false) {
            $ranking = $index + 1;
        } else {
            $ranking = 0;
        }

        return $ranking;
    }
}