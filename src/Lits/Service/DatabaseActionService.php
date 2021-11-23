<?php

declare(strict_types=1);

namespace Lits\Service;

use Lits\Database;

final class DatabaseActionService
{
    public ActionService $service;
    public Database $database;

    public function __construct(ActionService $service, Database $database)
    {
        $this->service = $service;
        $this->database = $database;
    }
}
