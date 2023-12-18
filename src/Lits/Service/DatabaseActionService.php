<?php

declare(strict_types=1);

namespace Lits\Service;

use Lits\Database;

final class DatabaseActionService
{
    public function __construct(
        public ActionService $service,
        public Database $database,
    ) {
    }
}
