<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppCustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Jenssegers\Agent\Agent;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppCustomAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setUserData([
                'ip' => $request->getClientIp(),
                'browser' => $this->getUserBrowser($request),
            ]);

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    public function getUserBrowser(Request $request)
    {
        // Get the user agent string from the request headers
        $userAgentString = $request->headers->get('User-Agent');

        // Create an instance of the Agent class
        $agent = new Agent();

        // Set the user agent string for parsing
        $agent->setUserAgent($userAgentString);

        // Get browser-related information
        $browserName = $agent->browser();
        $browserVersion = $agent->version($browserName);
        $browserPlatform = $agent->platform();

        // Return browser information
        return [
            'name' => $browserName,
            'version' => $browserVersion,
            'platform' => $browserPlatform,
        ];
    }
}
