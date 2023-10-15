<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageController extends AbstractController
{
    public function showImageAction(string $imageName)
    {

        $imagePath = $this->getParameter('kernel.project_dir') . '/public/images/' . $imageName;

        // Check if the image file exists.
        if (file_exists($imagePath)) {
            // Create a BinaryFileResponse for the image.
            $response = new BinaryFileResponse($imagePath);

            return $response;
        }

        // Handle the case when the image file is not found.
        throw $this->createNotFoundException('Image not found');
    }
}
