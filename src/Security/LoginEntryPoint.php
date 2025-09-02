<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\{RedirectResponse, Request, Response, JsonResponse};
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginEntryPoint implements AuthenticationEntryPointInterface
{
    public function __construct(public readonly UrlGeneratorInterface $urlGenerator) {}

    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        if ($request->isXmlHttpRequest() || $request->getContentTypeFormat() === 'json') {
            return new JsonResponse(['status' => 401, 'error' => 'Authentication required'], 401);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_auth_login'));
    }
}
