<?php

declare(strict_types=1);

namespace Lits\Service;

use Lits\Database;

final class DatabaseCommandService
{
    public function __construct(
        public CommandService $service,
        public Database $database,
    ) {
    }
}
