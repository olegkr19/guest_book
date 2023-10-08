<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CaptchaGenerator
{
    // private $session;

    public function __construct()
    {
        // $this->session = $session;
    }

    public function generateCaptchaImage(int $width = 150, int $height = 50, int $length = 6): string
    {
        // Generate a random alphanumeric string
        $captchaText = $this->generateRandomString($length);

        // Store the CAPTCHA text in the session for validation
        // $this->session->set('captcha_text', $captchaText);

        // Create an image with GD library
        $image = imagecreatetruecolor($width, $height);
        $bgColor = imagecolorallocate($image, 255, 255, 255);
        $textColor = imagecolorallocate($image, 0, 0, 0);

        // Fill the background with white
        imagefill($image, 0, 0, $bgColor);

        // Write the CAPTCHA text on the image
        imagettftext($image, 20, 0, 10, 30, $textColor, '/fonts/Roboto-Black.ttf', $captchaText);

        // Output the image as base64
        ob_start();
        imagepng($image);
        $captchaImage = 'data:image/png;base64,' . base64_encode(ob_get_clean());

        return $captchaImage;
    }

    private function generateRandomString(int $length): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $captchaText = '';

        for ($i = 0; $i < $length; $i++) {
            $captchaText .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $captchaText;
    }
}
