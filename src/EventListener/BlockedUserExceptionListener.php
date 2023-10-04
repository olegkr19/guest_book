<?php

namespace App\EventListener;

use App\Exception\BlockedUserException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BlockedUserExceptionListener
{
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if ($exception instanceof BlockedUserException) {
            // Redirect to the login page with an error message
            $url = $this->urlGenerator->generate('app_login', ['error' => 'User is blocked']);
            $response = new RedirectResponse($url);

            // Set a custom response message
            $response->headers->set('X-Status-Code', 403); // You can use a different status code if needed
            $response->setContent('User is blocked. Please contact support.');

            $event->setResponse($response);
        }
    }
}
