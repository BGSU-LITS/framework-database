<?php

declare(strict_types=1);

namespace Lits\Adapter;

use Latitude\QueryBuilder\Query\SelectQuery;
use Lits\Database;
use Pagerfanta\Adapter\AdapterInterface as Adapter;

use function Latitude\QueryBuilder\alias;
use function Latitude\QueryBuilder\express;
use function Latitude\QueryBuilder\func;

/** @implements Adapter<array<array-key, mixed>> */
final class PaginationAdapter implements Adapter
{
    public function __construct(
        private Database $database,
        private SelectQuery $query,
    ) {
    }

    #[\Override]
    public function getNbResults(): int
    {
        $statement = $this->database->execute(
            $this->database->query
                ->select(func('count', '*'))
                ->from(alias(express('(%s)', $this->query), 'query')),
        );

        $count = (int) $statement->fetchColumn();

        if ($count > 0) {
            return $count;
        }

        return 0;
    }

    /** @return iterable<array-key, array<array-key, mixed>> */
    #[\Override]
    public function getSlice(int $offset, int $length): iterable
    {
        $statement = $this->database->execute(
            $this->query
                ->offset($offset)
                ->limit($length),
        );

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
