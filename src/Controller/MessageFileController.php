<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\MessageFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageFileController extends AbstractController
{
    #[Route('/message/file', name: 'app_message_file')]
    public function index(): Response
    {
        return $this->render('message_file/index.html.twig', [
            'controller_name' => 'MessageFileController',
        ]);
    }

    public function uploadFile(
        Request $request, 
        Message $message, 
        EntityManagerInterface $entityManager,
        ParameterBagInterface $parameterBag
    )
    {
        $file = $request->files->get('_file'); // Assuming your file input is named 'file'

        if ($file) {

            // Get the original file name
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            // Generate a unique file name to avoid collisions
            $fileName = $originalFilename.'-'.uniqid().'.'.$file->guessExtension();

            try {
                $file->move(
                    '/var/www/html/public/images/message_images',
                    $fileName
                );

                $messageFile = new MessageFile();

                // Handle further processing (e.g., save the file name to the database)
                $messageFile->setFilename($fileName);

                $messageFile->setMessageId($message);

                $entityManager->persist($messageFile);

                $entityManager->flush();
            } catch (FileException $e) {
                // Handle file upload error
                throw new FileException($e->getMessage());
            }
        }

        // Redirect or render a response as needed
    }
}
