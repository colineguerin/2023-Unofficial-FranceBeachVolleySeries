<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact', methods: ['GET', 'POST'])]
    public function new(Request $request, RequestStack $requestStack, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $from = $form->getData()['email'];

            $email = (new Email())
                ->from($from)
                ->to("contact@fbvs.com")
                ->subject("FBVS : Nouveau message")
                ->html($this->renderView('contact/email.html.twig', ['formData' => $formData]));

            $this->addFlash('success', 'Votre message a bien été envoyé.');
            $form = $this->createForm(ContactType::class);
            $mailer->send($email);

            return $this->render('contact/index.html.twig', [
                'form' => $form->createView(),
                'formData' => $formData,
            ]);
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
            'formData' => null,
        ]);
    }
}