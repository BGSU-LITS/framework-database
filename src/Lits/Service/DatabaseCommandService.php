<?php

declare(strict_types=1);

namespace Lits\Service;

use Lits\Database;

final class DatabaseCommandService
{
    public CommandService $service;
    public Database $database;

    public function __construct(CommandService $service, Database $database)
    {
        $this->service = $service;
        $this->database = $database;
    }
}
