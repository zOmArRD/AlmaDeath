<?php
declare(strict_types=1);

namespace build;

const HEADER = <<<'HEADER'
<?php
/*
 * Copyright © 2022 zOmArRD (omar@ghostlymc.live) - All Rights Reserved.
 *
 * This is an auto generated file, don't try to modify this by hand lol.
 *
 *  $$$$$$\  $$\                         $$$$$$$\                       $$\     $$\       
 * $$  __$$\ $$ |                        $$  __$$\                      $$ |    $$ |      
 * $$ /  $$ |$$ |$$$$$$\$$$\   $$$$$$\  $$ |  $$ | $$$$$$\   $$$$$$\ $$$$$$\   $$$$$$$\  
 * $$$$$$$$ |$$ |$$  _$$  _$$\  \____$$\ $$ |  $$ |$$  __$$\  \____\\_$$  _|  $$  __$$\ 
 * $$  __$$ |$$ |$$ / $$ / $$ | $$$$$$$ |$$ |  $$ |$$$$$$$$ | $$$$$$$ | $$ |    $$ |  $$ |
 * $$ |  $$ |$$ |$$ | $$ | $$ |$$  __$$ |$$ |  $$ |$$   ____|$$  __$$ | $$ |$$\ $$ |  $$ |
 * $$ |  $$ |$$ |$$ | $$ | $$ |\$$$$$$ |$$$$$$$  |\$$$$$$\ \$$$$$$ | \$$$  |$$ |  $$ |
 * \__|  \__|\__|\__| \__| \__| \_______|\_______/  \_______| \_______|  \____/ \__|  \__|
 *                                                                                        
 */
declare(strict_types=1);

namespace zomarrd\almadeath\permission;


HEADER;

use Generator;

function stringifyKeys(array $array): Generator
{
    foreach ($array as $key => $value) {
        yield (string)$key => $value;
    }
}

function constantify(string $permissionName): string
{
    return strtoupper(str_replace([".", "-"], "_", $permissionName));
}

function generate_permission_keys(array $array): void
{
    ob_start();
    echo HEADER;
    echo <<<'HEADER'
/**
 * This class is generated automatically, do NOT modify it by hand.
 */
final class PermissionKey
{

HEADER;

    ksort($array, SORT_STRING);
    foreach (stringifyKeys($array) as $key => $_) {
        echo "\tpublic const ";
        echo constantify($key);
        echo " = \"" . $key . "\";\n";
    }

    echo "}";
    file_put_contents(dirname(__DIR__) . '/src/zomarrd/almadeath/permission/PermissionKey.php', ob_get_clean());
    echo "Done generating PermissionKey.\n";
}

$files = scandir(dirname(__DIR__));
foreach ($files as $file) {
    if (str_contains($file, 'plugin.yml')) {
        $yml = yaml_parse_file($file);
    }
}
if ($yml === false) {
    fwrite(STDERR, "Missing Permission files!\n");
    exit(1);
}
generate_permission_keys($yml["permissions"]);