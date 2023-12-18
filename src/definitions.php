<?php

declare(strict_types=1);

use Lits\Config\DatabaseConfig;
use Lits\Database;
use Lits\Framework;
use Lits\Settings;

return function (Framework $framework): void {
    $framework->addDefinition(
        Database::class,
        function (Settings $settings): Database {
            assert($settings['database'] instanceof DatabaseConfig);

            return new Database($settings['database']);
        },
    );
};
