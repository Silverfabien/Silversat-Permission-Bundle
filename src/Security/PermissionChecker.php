<?php

namespace Silversat\PermissionBundle\Security;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PermissionChecker
{
    private string $site;
    private RoleHierarchy $roleHierarchy;

    public function __construct(RoleHierarchy $roleHierarchy, string $site)
    {
        $this->roleHierarchy = $roleHierarchy;
        $this->site = $site;
    }

    public function isPermissionGranted(array $jwtRoles, string $requiredRole): bool
    {
        $userRoles = $this->extractSiteRole($jwtRoles);

        if ($userRoles === []) {
            return false;
        }

        $reachableRoles = $this->roleHierarchy->getReachableRoles($userRoles);

        return in_array($requiredRole, $reachableRoles, true);
    }

    public function denyPermissionUnlessGranted(array $jwtRoles, string $requiredRole): void
    {
        $userRoles = $this->extractSiteRole($jwtRoles);

        if ($userRoles === []) {
            throw new AccessDeniedHttpException('Not roles found.');
        }

        $reachableRoles = $this->roleHierarchy->getReachableRoles($userRoles);

        if (!in_array($requiredRole, $reachableRoles, true)) {
            throw new AccessDeniedHttpException('Access denied.');
        }
    }

    public function extractSiteRole(array $input): array
    {
        $rolesMap = $input['roles'] ?? $input;

        if (!is_array($rolesMap)) {
            return [];
        }

        $role = $rolesMap[$this->site] ?? null;

        if (is_string($role) && $role !== '') {
            return [$role];
        }

        return [];
    }
}