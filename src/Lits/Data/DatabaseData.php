<?php

declare(strict_types=1);

namespace Lits\Data;

use Lits\Data;
use Lits\Database;
use Lits\Exception\InvalidDataException;
use Lits\Settings;
use Safe\DateTimeImmutable;

abstract class DatabaseData extends Data
{
    protected Database $database;

    public function __construct(Settings $settings, Database $database)
    {
        parent::__construct($settings);

        $this->database = $database;
    }

    /**
     * @param array<string, string|null> $row
     * @throws InvalidDataException
     */
    protected static function findRowDatetime(
        array $row,
        string $key
    ): ?DateTimeImmutable {
        if (isset($row[$key])) {
            try {
                return new DateTimeImmutable($row[$key]);
            } catch (\Throwable $exception) {
                throw new InvalidDataException(
                    'The string could not be parsed into a datetime',
                    0,
                    $exception
                );
            }
        }

        return null;
    }

    /** @param array<string, string|null> $row */
    protected static function findRowBool(array $row, string $key): ?bool
    {
        if (isset($row[$key])) {
            return (bool) $row[$key];
        }

        return null;
    }

    /** @param array<string, string|null> $row */
    protected static function findRowFloat(array $row, string $key): ?float
    {
        if (isset($row[$key])) {
            return (float) $row[$key];
        }

        return null;
    }

    /** @param array<string, string|null> $row */
    protected static function findRowInt(array $row, string $key): ?int
    {
        if (isset($row[$key])) {
            return (int) $row[$key];
        }

        return null;
    }

    /** @param array<string, string|null> $row */
    protected static function findRowString(array $row, string $key): ?string
    {
        if (isset($row[$key])) {
            return \trim($row[$key]);
        }

        return null;
    }
}
