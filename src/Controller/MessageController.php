<?php

namespace App\Controller;

use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class MessageController extends AbstractController
{

    #[Route('/', name: 'message_list')]
    public function index(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator): Response
    {   
        $repository = $entityManager->getRepository(Message::class);

        // Get the request parameters for sorting
        $sortBy = $request->query->get('sort_by', 'id'); // Default sorting by id
        $sortOrder = $request->query->get('sort_order', 'desc'); // Default sorting in descending order

        $messages = $repository->findAllMessages($sortBy, $sortOrder);

        $user = $this->getUser();

        //check on auth user
        if (isset($user) && $user) {
            $messages = $repository->findAllMessagesByUser($user, $sortBy, $sortOrder);
        }

        $messages = $paginator->paginate(
            $messages,
            $request->query->getInt('page', 1), // Current page number
            25 // Number of items per page
        );

        $totalItemCount = $messages->getTotalItemCount();
        $itemsPerPage = $messages->getItemNumberPerPage();
        $currentPage = $messages->getCurrentPageNumber();

        $totalPages = ceil($totalItemCount / $itemsPerPage);

        // Define how many page links to show on each side of the current page.
        $sideLinks = 2;

        $startPage = max(1, $currentPage - $sideLinks);
        $endPage = min($totalPages, $currentPage + $sideLinks);

        // dd($endPage);

        return $this->render('message/index.html.twig', [
            'messages' => $messages,
            'query' => [
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ],
            'startPage' => $startPage,
            'endPage' => $endPage,
            'totalItemCount' => $totalItemCount,
            'itemsPerPage' => $itemsPerPage,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages
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
        $message->setCoordination(false);

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
