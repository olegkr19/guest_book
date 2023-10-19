<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\MessageFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    #[Route('/messages/card/{id}', name: 'message_show_card')]
    public function findOne($id, EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Message::class);

        $message = $repository->find($id);

        if (!$message) {
            throw $this->createNotFoundException();
        }

        $fileMessageRepository = $entityManager->getRepository(MessageFile::class);

        $filesByMessage = $fileMessageRepository->findFilesByMessageId($id);

        return $this->render('message/show.html.twig', [
            'message' => $message,
            'files' => $filesByMessage,
            'delete_image' => false
        ]);
    }

    #[Route('/messages/edit_card/{id}', name: 'message_edit_card')]
    public function cardForEdit($id, EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Message::class);

        $message = $repository->find($id);

        if (!$message) {
            throw $this->createNotFoundException();
        }

        $fileMessageRepository = $entityManager->getRepository(MessageFile::class);

        $filesByMessage = $fileMessageRepository->findFilesByMessageId($id);

        return $this->render('message/edit.html.twig', [
            'message' => $message,
            'files' => $filesByMessage,
            'delete_image' => true
        ]);
    }

    #[Route('/messages/add', name: 'message_add', priority: 2)]
    public function add(Request $request, 
    EntityManagerInterface $entityManager,
    ValidatorInterface $validator
    ): Response
    {
        $message = new Message();

        // Get the user's input and the CAPTCHA code from the session
        $userInput = $request->request->get('_captcha');
        $captchaCode = $request->getSession()->get('captcha_code');

        // Compare the user's input to the stored CAPTCHA code
        if ($userInput !== $captchaCode) {
            // CAPTCHA validation failed, handle the error
            $this->addFlash('error', 'CAPTCHA code is incorrect. Please try again.');

            // Redirect back to the form or take any other appropriate action
            return $this->redirectToRoute('message_list');
        }

        // Clear the CAPTCHA code from the session
        $request->getSession()->remove('captcha_code');

        $message->setUsername(htmlspecialchars($request->request->get('_username'), ENT_QUOTES, 'UTF-8'));
        $message->setEmail(htmlspecialchars($request->request->get('_email'), ENT_QUOTES, 'UTF-8'));
        $message->setHomepage(htmlspecialchars($request->request->get('_homepage'), ENT_QUOTES, 'UTF-8'));
        $message->setText(htmlspecialchars($request->request->get('_text'), ENT_QUOTES, 'UTF-8'));
        $message->setCreatedAt(date('Y-m-d H:i:s'));
        $message->setCoordination(true);

        $entityManager->persist($message);

        $entityManager->flush();

        $messageFile = new MessageFileController();

        $file = $messageFile->getFile($request);

        if ($file) {

            // Validate the file type using Symfony's File constraint
            $constraints = new File([
                'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG, GIF).',
            ]);
    
            $errors = $validator->validate($file, $constraints);
    
            if (count($errors) > 0) {
                $this->addFlash('error', 'Not an image was uploaded. Please upload a valid image file (JPEG, PNG, GIF).');
    
                return $this->redirectToRoute('message_list');
            }
        }

        $messageFile->uploadFile($request, $message, $entityManager);

        //Add a flash message
        $this->addFlash('success', 'Your message has been added. ');

        return $this->redirectToRoute('message_list');
    }

    #[Route('/messages/edit/{id}', name: 'message_edit')]
    public function edit($id, 
    Request $request, 
    EntityManagerInterface $entityManager,
    ValidatorInterface $validator
    ): Response
    {
        $repository = $entityManager->getRepository(Message::class);

        $message = $repository->find($id);

        if (!$message) {
            throw $this->createNotFoundException();
        }

        $message->setUsername(htmlspecialchars($request->request->get('_username'), ENT_QUOTES, 'UTF-8'));
        $message->setEmail(htmlspecialchars($request->request->get('_email'), ENT_QUOTES, 'UTF-8'));
        $message->setHomepage(htmlspecialchars($request->request->get('_homepage'), ENT_QUOTES, 'UTF-8'));
        $message->setText(htmlspecialchars($request->request->get('_text'), ENT_QUOTES, 'UTF-8'));

        $entityManager->persist($message);

        $entityManager->flush();

        $messageFile = new MessageFileController();

        $file = $messageFile->getFile($request);

        if ($file) {

            // Validate the file type using Symfony's File constraint
            $constraints = new File([
                'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG, GIF).',
            ]);
    
            $errors = $validator->validate($file, $constraints);
    
            if (count($errors) > 0) {
                $this->addFlash('error', 'Not an image was uploaded. Please upload a valid image file (JPEG, PNG, GIF).');
    
                return $this->redirectToRoute('message_list');
            }
        }

        $messageFile->uploadFile($request, $message, $entityManager);

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
