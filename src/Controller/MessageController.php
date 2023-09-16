<?php

namespace App\Controller;

use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{

    #[Route('/', name: 'message_list')]
    public function index(EntityManagerInterface $entityManager): Response
    {   
        $repository = $entityManager->getRepository(Message::class);

        $messages = $repository->findAllMessages();

        $user = $this->getUser();

        //check on auth user
        if (isset($user) && $user) {
            $messages = $repository->findAllMessagesByUser($user);   
        }

        return $this->render('message/index.html.twig', [
            'messages' => $messages
        ]);
    }

    #[Route('/messages/{id}', name: 'message_card')]
    public function findOne($id, EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Message::class);

        $message = $repository->find($id);

        if (!$message) {
            throw $this->createNotFoundException();
        }

        return $this->render('message/edit.html.twig', [
            'message' => $message
        ]);
    }

    #[Route('/messages/add', name: 'message_add', priority: 2)]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $message = new Message();

        $message->setUsername($request->request->get('_username'));
        $message->setEmail($request->request->get('_email'));
        $message->setHomepage($request->request->get('_homepage'));
        $message->setText($request->request->get('_text'));
        $message->setCreatedAt(date('Y-m-d H:i:s'));

        $entityManager->persist($message);

        $entityManager->flush();

        //Add a flash message
        $this->addFlash('success', 'Your message has been added');

        return $this->redirectToRoute('message_list');
    }

    #[Route('/messages/edit/{id}', name: 'message_edit')]
    public function edit($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Message::class);

        $message = $repository->find($id);

        if (!$message) {
            throw $this->createNotFoundException();
        }

        $message->setUsername($request->request->get('_username'));
        $message->setEmail($request->request->get('_email'));
        $message->setHomepage($request->request->get('_homepage'));
        $message->setText($request->request->get('_text'));

        $entityManager->persist($message);

        $entityManager->flush();

        //Add a flash message
        $this->addFlash('success', 'Your message has been updated');

        return $this->redirectToRoute('message_list'); 
    }

    #[Route('/messages/delete/{id}', name: 'message_delete')]
    public function delete($id, EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Message::class);

        $message = $repository->find($id);
    
        $entityManager->remove($message);
    
        $entityManager->flush();

        //Add a flash message
        $this->addFlash('success', 'Your message has been deleted');

        return $this->redirectToRoute('message_list');
    }
}
