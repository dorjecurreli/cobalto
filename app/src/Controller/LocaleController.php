<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;

class LocaleController extends AbstractController
{
    public function __construct(private RouterInterface $router)
    {
    }

    #[Route('/switch-language/{locale}', name: 'app_switch_language', requirements: ['locale' => '%app.supported_locales%'])]
    public function switchLanguage(Request $request, string $locale): RedirectResponse
    {

        $referer = $request->headers->get('referer');

        if (!$referer) {
            return new RedirectResponse($this->router->generate('app_home'));
        }

        $parsedUrl = parse_url($referer);

        $path = $parsedUrl['path'];

        $pathParts = explode('/', trim($path, '/'));

        $pathParts[0] = $locale;

        $newPath = '/' . implode('/', $pathParts);

        $newUrl = (isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '') .
            (isset($parsedUrl['host']) ? $parsedUrl['host'] : '') .
            (isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '') .
            $newPath;

        return new RedirectResponse($newUrl);
    }

}
