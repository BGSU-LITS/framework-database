<?php

declare(strict_types=1);

namespace Lits\Config;

use Lits\Config;
use Lits\Exception\InvalidConfigException;

final class DatabaseConfig extends Config
{
    public string $type = 'mysql';
    public string $host = '';
    public ?int $port = null;
    public string $name = '';
    public string $username = '';
    public string $password = '';
    public string $prefix = '';
    public ?string $migration = null;

    /** @throws InvalidConfigException */
    public function testSettings(): void
    {
        if (!\in_array($this->type, ['mysql', 'pgsql'], true)) {
            throw new InvalidConfigException(
                'The database type must be mysql or pgsql'
            );
        }

        if ($this->host === '') {
            throw new InvalidConfigException(
                'The database host must be specified'
            );
        }

        if ($this->username === '') {
            throw new InvalidConfigException(
                'The database username must be specified'
            );
        }

        if ($this->password === '') {
            throw new InvalidConfigException(
                'The database password must be specified'
            );
        }

        if ($this->name === '') {
            throw new InvalidConfigException(
                'The database name must be specified'
            );
        }
    }
}
