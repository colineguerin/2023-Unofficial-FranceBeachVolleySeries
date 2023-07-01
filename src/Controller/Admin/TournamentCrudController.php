<?php

namespace App\Controller\Admin;

use App\Entity\Tournament;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

class TournamentCrudController extends AbstractCrudController
{
    public CONST ACTION_DUPLICATE = 'duplicate';
    public static function getEntityFqcn(): string
    {
        return Tournament::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $duplicate = Action::new(self::ACTION_DUPLICATE)
            ->linkToCrudAction('duplicateTournament');
        return $actions
            ->add(Crud::PAGE_EDIT, $duplicate);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name', 'Nom du tournoi');
        yield TextField::new('category', 'Catégorie et genre')->setRequired(true);
        yield TextField::new('location', 'Lieu')->setRequired(true);
        yield DateTimeField::new('tournamentDate', 'Date et heure')->setRequired(true);
        yield AssociationField::new('club', 'Club organisateur')->setRequired(true);
        yield IntegerField::new('maxTeam', 'Nombre d\'équipes maximum');
        yield TextEditorField::new('details', 'Détails');
    }

    public function duplicateTournament(
        AdminContext $context,
        AdminUrlGenerator $adminUrlGenerator,
        EntityManagerInterface $entityManager
    ): Response
    {
        /** @var Tournament $tournament */
        $tournament = $context->getEntity()->getInstance();

        $duplicatedTournament = clone $tournament;
        parent::persistEntity($entityManager, $duplicatedTournament);

        $url = $adminUrlGenerator->setController(self::class)
            ->setAction(Action::DETAIL)
            ->setEntityId($duplicatedTournament->getId())
            ->generateUrl();

        return $this->redirect($url);
    }
}
