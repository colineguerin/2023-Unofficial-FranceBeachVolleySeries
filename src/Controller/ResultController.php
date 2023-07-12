<?php

namespace App\Controller;

use App\Repository\ResultRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResultController extends AbstractController
{
    #[Route('/classement', name: 'app_result')]
    public function index(UserRepository $userRepository): Response
    {
        $players = $userRepository->findAll();

        return $this->render('result/index.html.twig', [
            'players' => $players,
        ]);
    }
}
