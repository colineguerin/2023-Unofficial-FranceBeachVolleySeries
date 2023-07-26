<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\SearchAllType;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/équipe')]
class TeamController extends AbstractController
{
    private TeamRepository $teamRepository;
    private UserRepository $userRepository;

    public function __construct(TeamRepository $teamRepository, UserRepository $userRepository)
    {
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
    }

    #[Route('/créer', name: 'app_team_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Security $security, MailerInterface $mailer): Response
    {
        $userId = $security->getUser()->getId();
        $user = $this->userRepository->findOneBy(['id' => $userId]);

        $searchForm = $this->createForm(SearchAllType::class);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            try {
                $permitNumber = $searchForm->get('search')->getData();
                $partner = $this->userRepository->findOneBy(['permitNumber' => $permitNumber]);

                if (!$partner) {
                    throw new \InvalidArgumentException('Ce numéro de licence n\'est pas attribué.');
                }

                if ($partner === $user) {
                    throw new \InvalidArgumentException('Vous ne pouvez pas choisir votre propre numéro de licence.');
                }

                $existingTeam = $this->teamRepository->findOneByPlayers($user, $partner);
                if ($existingTeam) {
                    throw new \InvalidArgumentException('Cette équipe existe déjà.');
                }

                $team = new Team();
                $team->addPlayer($user);
                $team->addPlayer($partner);
                $team->setCreatedAt(new \DateTimeImmutable());
                $team->setIsActive(true);
                $team->setIsValidated(false);

                $this->teamRepository->save($team, true);

                $email = (new Email())
                    ->from($this->getParameter('mailer_from'))
                    ->to($partner->getEmail())
                    ->subject("FBVS : Nouvelle équipe à valider")
                    ->html($this->renderView('team/email.html.twig', [
                        'partner' => $partner,
                        'user' => $user,
                        ])
                    );
                $this->addFlash('success', 'Votre demande a bien été enregistrée. Un email vient d\'être envoyé à votre partenaire pour valider votre nouvelle équipe.');
                $mailer->send($email);

                return $this->redirectToRoute('app_user_show', ['id' => $security->getUser()->getId()], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('team/new.html.twig', [
            'searchForm' => $searchForm,
        ]);
    }

    #[Route('/{id}/reactivate', name: 'app_team_reactivate', methods: ['GET', 'POST'])]
    public function reactivate(Request $request, Team $team, Security $security): Response
    {
        if ($this->isCsrfTokenValid('reactivate' . $team->getId(), $request->request->get('_token'))) {
            $team->setIsActive(true);
            $team->setUpdatedAt(new \DateTimeImmutable());
            $this->teamRepository->save($team, true);
            $this->addFlash('success', 'Votre équipe a bien été réactivée. Vous pouvez à nouveau vous inscrire à des tournois avec cette équipe.');
        }

        return $this->redirectToRoute('app_user_show', ['id' => $security->getUser()->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/validate', name: 'app_team_validate', methods: ['GET', 'POST'])]
    public function validate(Request $request, Team $team, Security $security): Response
    {
        if ($this->isCsrfTokenValid('validate' . $team->getId(), $request->request->get('_token'))) {
            $team->setIsValidated(true);
            $team->setUpdatedAt(new \DateTimeImmutable());
            $this->teamRepository->save($team, true);
            $this->addFlash('success', 'Votre nouvelle équipe a bien été validée. Vous pouvez désormais vous inscrire à des tournois avec cette nouvelle équipe.');
        }

        return $this->redirectToRoute('app_user_show', ['id' => $security->getUser()->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/delete', name: 'app_team_delete', methods: ['POST'])]
    public function delete(Request $request, Team $team, Security $security): Response
    {
        if ($this->isCsrfTokenValid('delete' . $team->getId(), $request->request->get('_token'))) {
            $this->teamRepository->remove($team, true);
            $this->addFlash('success', 'Votre équipe a bien été supprimée.');
        }

        return $this->redirectToRoute('app_user_show', ['id' => $security->getUser()->getId()], Response::HTTP_SEE_OTHER);
    }
}
