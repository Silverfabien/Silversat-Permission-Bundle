<?php

namespace Silversat\PermissionBundle\Security;

class RoleHierarchy
{
    private array $hierarchy;

    public function __construct(array $hierarchy)
    {
        $this->hierarchy = $hierarchy;
    }

    public function getReachableRoles(array $roles): array
    {
        $allRoles= $roles;
        $toProcess = $roles;

        while (!empty($toProcess)) {
            $role = array_pop($toProcess);

            if (isset($this->hierarchy[$role])) {
                foreach ((array)$this->hierarchy[$role] as $reachableRole) {
                    if (!in_array($reachableRole, $allRoles)) {
                        $allRoles[] = $reachableRole;
                        $toProcess[] = $reachableRole;
                    }
                }
            }
        }

        return $allRoles;
    }
}