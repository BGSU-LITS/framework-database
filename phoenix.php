<?php

declare(strict_types=1);

use Lits\Database;

$path = getcwd();

$path = ($path === false ? '.' : $path) .
    DIRECTORY_SEPARATOR . 'src' .
    DIRECTORY_SEPARATOR . 'framework.php';

if (!file_exists($path)) {
    return [];
}

/** @var \Lits\Framework $framework */
$framework = require $path;

/** @var \Lits\Database $database */
$database = $framework->container()->get(Database::class);

return $database->migration();
