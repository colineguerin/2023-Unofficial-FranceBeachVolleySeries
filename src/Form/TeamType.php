<?php

namespace App\Form;

use App\Entity\Team;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentUser = $options['current_user'];

        $builder
            ->add('players', EntityType::class, [
                'class' => User::class,
                'label' => false,
                'choice_label' => 'permitNumber',
                'multiple' => true,
                'expanded' => false,
                'query_builder' => function (EntityRepository $er) use ($currentUser) {
                    return $er->createQueryBuilder('u')
                        ->andWhere('u != :current_user')
                        ->setParameter('current_user', $currentUser);
                },
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
            'current_user' => null,
        ]);
        $resolver->setAllowedTypes('current_user', User::class);
    }
}
