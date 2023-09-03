<?php

namespace App\Controller;

use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    private function getForm(Message $message)
    {
        $dateTime = new \DateTimeImmutable();

        return $this->createFormBuilder($message)
        ->add('username')
        ->add('email', EmailType::class)
        ->add('homepage')
        ->add('text', TextareaType::class)
        ->add('created_at', HiddenType::class, [
            'data' => $dateTime->format('Y-m-d H:i:s')
        ])
        ->add('save', SubmitType::class)
        ->getForm();
    }

    #[Route('/', name: 'message_list')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $message = new Message();

        $form = $this->getForm($message);

        $conn = $entityManager->getConnection();

        $where = 'id > 0';

        $prepareData = [];

        $user = $this->getUser();

        if (isset($user) && $user) {
            $where .= ' AND email = :email';
            $prepareData['email'] = $this->getUser()->getUserIdentifier();
        }
        
        $sql = '
            SELECT id, username, email, homepage, text, created_at
            FROM message
            WHERE '. $where .'
            ORDER BY id DESC
        ';

        $messages = $conn->prepare($sql)->executeQuery($prepareData)->fetchAllAssociative();

        return $this->render('message/index.html.twig', [
            'form' => $form,
            'messages' => $messages
        ]);
    }

    #[Route('/messages/add', name: 'message_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $message = new Message();

        $form = $this->getForm($message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $messageData = $form->getData();

            $entityManager->persist($messageData);

            $entityManager->flush();

            //Add a flash message
            $this->addFlash('success', 'Your message has been added');
        }

        return $this->redirectToRoute('message_list');
    }

    #[Route('/messages/edit/{id}', name: 'message_edit')]
    public function edit($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        dd($id);
        $message = new Message();

        $form = $this->getForm($message);

        // $form = $this->createForm(
        //     MessageType::class,
        //     $message
        // );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $messageData = $form->getData();

            $entityManager->persist($messageData);

            $entityManager->flush();

            //Add a flash message
            $this->addFlash('success', 'Your message has been updated');

            return $this->redirectToRoute('message_list');
        }
    }
}
