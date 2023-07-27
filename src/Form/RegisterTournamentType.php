<?php

namespace App\Form;

use App\Entity\User;
use App\Repository\TeamRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterTournamentType extends AbstractType
{
    private Security $security;
    private TeamRepository $teamRepository;

    /**
     * @param Security $security
     */
    public function __construct(Security $security, TeamRepository $teamRepository)
    {
        $this->security = $security;
        $this->teamRepository = $teamRepository;
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentUser = $this->security->getUser();
        $teams = $currentUser->getTeams();
        $teamChoices = [];

        foreach ($teams as $team) {
            if ($team->isIsValidated() && $team->isIsActive()) {
                $teamChoices[$team->__toString()] = $team;
            }
        }

        $builder
            ->add('team', ChoiceType::class, [
                'label' => false,
                'choices' => $teamChoices,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([

        ]);
    }
}
