<?php

declare(strict_types=1);

namespace Lits;

use Latitude\QueryBuilder\Query\SelectQuery;
use Lits\Adapter\PaginationAdapter;
use Pagerfanta\Pagerfanta;

/** @extends Pagerfanta<array<array-key, mixed>> */
final class Pagination extends Pagerfanta
{
    public function __construct(Database $database, SelectQuery $query)
    {
        parent::__construct(new PaginationAdapter($database, $query));
    }
}
