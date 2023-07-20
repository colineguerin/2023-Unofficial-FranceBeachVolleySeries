<?php

namespace App\Form;

use App\Entity\Team;
use App\Entity\Tournament;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterTournamentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentUser = $options['current_user'];

        $builder
            ->add('teams', EntityType::class, [
                'class' => Team::class,
                'label' => false,
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function (EntityRepository $er) use ($currentUser) {
                    return $er->createQueryBuilder('t')
                        ->innerJoin('t.players', 'p')
                        ->where('p.id = :userId')
                        ->setParameter('userId', $currentUser->getId());
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tournament::class,
            'current_user' => null,
        ]);
        $resolver->setAllowedTypes('current_user', User::class);
    }
}
