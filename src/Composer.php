<?php

namespace Silversat\PermissionBundle;

class Composer
{
    public static function postInstall($event = null): void
    {
        $projectDir = getcwd();
        $configDir = $projectDir.'/config/packages';

        if (!is_dir($configDir)) {
            @mkdir($configDir, 0777, true);
        }

        $file = $configDir.'/silversat_permission.yaml';

        if (!file_exists($file)) {
            $yaml = <<<YAML
                silversat_permission:
                    site: "Nom_du_site"
                    hierarchy:
                        "ROLE_ADMIN": ["ROLE_FRIEND"]
                        "ROLE_FRIEND": ["ROLE_USER"]
                    access_control:
                        - { path: ^/admin, roles: ROLE_ADMIN }
                        - { path: ^/account, roles: ROLE_USER }
            YAML;

            file_put_contents($file, $yaml.PHP_EOL);
        }
    }
}