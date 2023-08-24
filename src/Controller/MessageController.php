<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    private function getForm()
    {
        $message = new Message();

        return $this->createFormBuilder($message)
        ->add('username')
        ->add('email', EmailType::class)
        ->add('homepage')
        ->add('text', TextareaType::class)
        ->add('save', SubmitType::class)
        ->getForm();
    }

    #[Route('/', name: 'message_list')]
    public function index(): Response
    {
        $form = $this->getForm();

        return $this->render('message/index.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/messages/add', name: 'message_add')]
    public function add(Request $request, MessageRepository $doctrine): Response
    {
        $form = $this->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->getData();
            // $messages = $doctrine->getManager();
            $doctrine->persist($message);
            $doctrine->flush();

            //Add a flash message
            $this->addFlash('success', 'Your message has been added');
        }

        return $this->redirectToRoute('message_list');
    }
}
