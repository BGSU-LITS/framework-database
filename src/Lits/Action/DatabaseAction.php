<?php

declare(strict_types=1);

namespace Lits\Action;

use Lits\Action;
use Lits\Database;
use Lits\Service\DatabaseActionService;

abstract class DatabaseAction extends Action
{
    protected Database $database;

    public function __construct(DatabaseActionService $service)
    {
        parent::__construct($service->service);

        $this->database = $service->database;
    }
}
