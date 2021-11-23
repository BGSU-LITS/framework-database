<?php

declare(strict_types=1);

namespace Lits\Data;

use Lits\Data;
use Lits\Database;
use Lits\Settings;

abstract class DatabaseData extends Data
{
    protected Database $database;

    public function __construct(Settings $settings, Database $database)
    {
        parent::__construct($settings);

        $this->database = $database;
    }
}
