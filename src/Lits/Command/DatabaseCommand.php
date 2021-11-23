<?php

declare(strict_types=1);

namespace Lits\Command;

use Lits\Command;
use Lits\Database;
use Lits\Service\DatabaseCommandService;

abstract class DatabaseCommand extends Command
{
    protected Database $database;

    public function __construct(DatabaseCommandService $service)
    {
        parent::__construct($service->service);

        $this->database = $service->database;
    }
}
