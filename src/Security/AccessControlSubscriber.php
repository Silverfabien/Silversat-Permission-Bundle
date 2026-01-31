<?php

namespace Silversat\PermissionBundle\Security;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class AccessControlSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private PermissionChecker $permissionChecker,
        private array $rules,
        private string $site
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 0]
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if ($this->rules === []) {
            return;
        }

        $request = $event->getRequest();
        $pathInfo = $request->getPathInfo();

        foreach ($this->rules as $rule) {
            $pattern = $rule['path'] ?? null;
            $requiredRole = $rule['role'] ?? null;

            if (!is_string($pattern) || $pattern === '' || !is_string($requiredRole) || $requiredRole === '') {
                continue;
            }

            $match = @preg_match('#'.$pattern.'#', $pathInfo);
            if ($match !== 1) {
                continue;
            }

            if (!$request->hasSession()) {
                throw new AccessDeniedHttpException('Session non disponible.');
            }

            $currentRole = $request->getSession()->get('role');
            if (!is_string($currentRole) || $currentRole === '') {
                throw new AccessDeniedHttpException('Aucun rôle trouvé.');
            }

            $jwt = [
                'roles' => [
                    $this->site => $currentRole
                ]
            ];

            $this->permissionChecker->denyPermissionUnlessGranted($jwt, $requiredRole);
        }
    }
}