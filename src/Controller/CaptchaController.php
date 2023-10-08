<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Gregwar\Captcha\CaptchaBuilder;
use Symfony\Component\HttpFoundation\Request;

class CaptchaController extends AbstractController
{
    // #[Route('/captcha', name: 'app_captcha')]
    // public function index(): Response
    // {
    //     return $this->render('captcha/index.html.twig', [
    //         'controller_name' => 'CaptchaController',
    //     ]);
    // }

    #[Route('/generate-captcha', name: 'captcha_generate')]
    public function generate(Request $request): Response
    {
        // Generate a CAPTCHA image
        $captcha = new CaptchaBuilder();
        $captcha->build();
        $captchaCode = $captcha->getPhrase();

        // Store the CAPTCHA code in the session
        $request->getSession()->set('captcha_code', $captchaCode);

        // Create and return a response with the CAPTCHA image
        $imageData = $captcha->get();
        $response = new Response($imageData);
        $response->headers->set('Content-Type', 'image/jpeg');

        return $response;
    }
}
