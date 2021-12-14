<?php

declare(strict_types=1);

namespace Lits\Adapter;

use Latitude\QueryBuilder\ExpressionInterface as Expression;
use Latitude\QueryBuilder\Query\SelectQuery;
use Lits\Database;
use Pagerfanta\Adapter\AdapterInterface as Adapter;

use function Latitude\QueryBuilder\alias;
use function Latitude\QueryBuilder\express;
use function Latitude\QueryBuilder\func;

class PaginationAdapter implements Adapter
{
    private Database $database;
    private SelectQuery $query;

    public function __construct(Database $database, SelectQuery $query)
    {
        $this->database = $database;
        $this->query = $query;
    }

    public function getNbResults(): int
    {
        $statement = $this->database->execute(
            $this->database->query
                ->select(func('count', '*'))
                ->from($this->aliasQuery())
        );

        $count = (int) $statement->fetchColumn();

        if ($count > 0) {
            return $count;
        }

        return 0;
    }

    /** @return iterable<array-key, mixed> */
    public function getSlice(int $offset, int $length): iterable
    {
        $statement = $this->database->execute(
            $this->database->query
                ->select()
                ->from($this->aliasQuery())
                ->offset($offset)
                ->limit($length)
        );

        $slice = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (\is_array($slice)) {
            return $slice;
        }

        return [];
    }

    private function aliasQuery(): Expression
    {
        return alias(express('(%s)', $this->query), 'query');
    }
}
