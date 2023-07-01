<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Club;
use App\Entity\User;
use App\Entity\Team;
use App\Entity\Tournament;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/home.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('France Beach Volley Series');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::subMenu('Articles', 'fas fa-newspaper')->setSubItems([
            MenuItem::linkToCrud('Ajouter un article', 'fas fa-plus', Article::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Afficher les articles', 'fas fa-eye', Article::class)
        ]);
        yield MenuItem::subMenu('Clubs', 'fas fa-shield')->setSubItems([
            MenuItem::linkToCrud('Ajouter un club', 'fas fa-plus', Club::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Afficher les clubs', 'fas fa-eye', Club::class)
        ]);
        yield MenuItem::subMenu('Joueurs', 'fas fa-user')->setSubItems([
            MenuItem::linkToCrud('Ajouter un joueur', 'fas fa-plus', User::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Afficher les joueurs', 'fas fa-eye', User::class)
        ]);
        yield MenuItem::subMenu('Equipes', 'fas fa-user-group')->setSubItems([
            MenuItem::linkToCrud('Ajouter une équipe', 'fas fa-plus', Team::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Afficher les équipes', 'fas fa-eye', Team::class)
        ]);
        yield MenuItem::subMenu('Tournois', 'fas fa-volleyball')->setSubItems([
            MenuItem::linkToCrud('Ajouter un tournoi', 'fas fa-plus', Tournament::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Afficher les tournois', 'fas fa-eye', Tournament::class)
        ]);
        yield MenuItem::subMenu('Results', 'fas fa-trophy')->setSubItems([
        ]);
        yield MenuItem::linkToRoute('Quitter le dashboard', 'fas fa-right-from-bracket', 'app_index');
    }
}