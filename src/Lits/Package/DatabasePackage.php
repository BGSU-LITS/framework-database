<?php

declare(strict_types=1);

namespace Lits\Package;

use Lits\Package;

final class DatabasePackage extends Package
{
    #[\Override]
    public function path(): string
    {
        return \dirname(\dirname(__DIR__));
    }
}
