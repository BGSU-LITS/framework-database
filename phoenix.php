<?php

declare(strict_types=1);

use Lits\Database;
use Lits\Framework;

$path = getcwd();

$path = ($path === false ? '.' : $path) .
    DIRECTORY_SEPARATOR . 'src' .
    DIRECTORY_SEPARATOR . 'framework.php';

if (!file_exists($path)) {
    return [];
}

$framework = require $path;
assert($framework instanceof Framework);

$database = $framework->container()->get(Database::class);
assert($database instanceof Database);

return $database->migration();
