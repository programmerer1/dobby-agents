<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Csrf\{CsrfToken, CsrfTokenManagerInterface};
use Symfony\Component\HttpFoundation\{JsonResponse, RedirectResponse, Session};
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class CsrfRequestListener
{
    public function __construct(
        public readonly CsrfTokenManagerInterface $csrfTokenManager,
        public readonly UrlGeneratorInterface $urlGenerator
    ) {}

    #[AsEventListener]
    public function onRequestEvent(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return;
        }

        $submittedToken = $request->headers->get('X-CSRF-TOKEN');

        if (!$submittedToken || !$this->csrfTokenManager->isTokenValid(new CsrfToken('my_csrf', $submittedToken))) {
            if ($request->isXmlHttpRequest() || $request->getContentTypeFormat() === 'json') {
                $event->setResponse(new JsonResponse([
                    'status' => 419,
                    'error' => 'Invalid request',
                ], 419));

                return;
            }

            /** @var Session $session*/
            $session = $request->getSession();

            $session->getFlashBag()->add('error', 'Invalid request');
            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_home')));
        }
    }
}
