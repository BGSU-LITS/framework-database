<?php

declare(strict_types=1);

use Lits\Config\DatabaseConfig;
use Lits\Framework;

return function (Framework $framework): void {
    $framework->addConfig('database', new DatabaseConfig());
};
