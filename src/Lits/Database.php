<?php

declare(strict_types=1);

namespace Lits;

use Latitude\QueryBuilder\Engine\CommonEngine;
use Latitude\QueryBuilder\Engine\MySqlEngine;
use Latitude\QueryBuilder\Engine\PostgresEngine;
use Latitude\QueryBuilder\Query\SelectQuery;
use Latitude\QueryBuilder\QueryFactory;
use Latitude\QueryBuilder\QueryInterface as Query;
use Lits\Config\DatabaseConfig;
use Lits\Exception\DuplicateInsertException;
use Lits\Exception\InvalidConfigException;

use function Latitude\QueryBuilder\field;

final class Database
{
    public \PDO $pdo;
    public QueryFactory $query;
    public string $prefix = '';

    /** @throws InvalidConfigException */
    public function __construct(public DatabaseConfig $config)
    {
        $config->testSettings();

        $dsn = $config->type . ':host=' . $config->host;

        if (\is_int($config->port)) {
            $dsn .= ';port=' . (string) $config->port;
        }

        $dsn .= ';dbname=' . $config->name;

        $this->pdo = new \PDO(
            $dsn,
            $config->username,
            $config->password,
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION],
        );

        if ($config->type === 'mysql') {
            $engine = new MySqlEngine();
        } elseif ($config->type === 'pgsql') {
            $engine = new PostgresEngine();
        } else {
            $engine = new CommonEngine();
        }

        $this->query = new QueryFactory($engine);
        $this->prefix = $config->prefix;
    }

    /**
     * @param array<string, ?scalar> $map
     * @throws \PDOException
     */
    public function delete(string $table, array $map): void
    {
        $query = $this->query->delete($this->prefix . $table);

        foreach ($map as $key => $value) {
            $query->andWhere(field($key)->eq($value));
        }

        $this->execute($query);
    }

    public function execute(Query $query): \PDOStatement
    {
        $compiled = $query->compile();

        $statement = $this->pdo->prepare($compiled->sql());
        $statement->execute($compiled->params());

        return $statement;
    }

    /**
     * @param array<string, ?scalar> $map_unique
     * @throws \PDOException
     */
    public function findId(
        string $table,
        string $field_id,
        array $map_unique,
    ): int {
        $query = $this->query
            ->select($field_id)
            ->from($this->prefix . $table);

        foreach ($map_unique as $key => $value) {
            $query->andWhere(field($key)->eq($value));
        }

        $statement = $this->execute($query->limit(1));

        return (int) $statement->fetchColumn();
    }

    /**
     * @param array<string, ?scalar> $map
     * @throws DuplicateInsertException
     * @throws \PDOException
     */
    public function insert(string $table, array $map): ?int
    {
        try {
            $this->execute(
                $this->query->insert($this->prefix . $table, $map),
            );

            $id = (int) $this->pdo->lastInsertId();
        } catch (\PDOException $exception) {
            if ($exception->getCode() === '23000') {
                throw new DuplicateInsertException(
                    'Could not insert duplicate values',
                    0,
                    $exception,
                );
            }

            throw $exception;
        }

        if ($id > 0) {
            return $id;
        }

        return null;
    }

    /**
     * @param array<string, ?scalar> $map_unique
     * @param array<string, ?scalar> $map_other
     * @throws \PDOException
     */
    public function insertIgnore(
        string $table,
        array $map_unique,
        array $map_other = [],
        ?string $field_id = null,
    ): ?int {
        try {
            $id = $this->insert($table, $map_unique + $map_other);
        } catch (DuplicateInsertException) {
            if (\is_null($field_id)) {
                return null;
            }

            $id = $this->findId($table, $field_id, $map_unique);
        }

        if ($id > 0) {
            return $id;
        }

        return null;
    }

    /**
     * @param array<string, ?scalar> $map_unique
     * @param array<string, ?scalar> $map_other
     * @throws \PDOException
     */
    public function insertOrUpdate(
        string $table,
        array $map_unique,
        array $map_other,
        string $field_id,
    ): ?int {
        $id = $this->insertIgnore($table, $map_unique, $map_other, $field_id);

        if (!\is_null($id)) {
            $this->update($table, $map_other, $field_id, $id);
        }

        return $id;
    }

    /** @return array<mixed> */
    public function migration(): array
    {
        return [
            'default_environment' => 'default',
            'environments' => [
                'default' => [
                    'adapter' => $this->config->type,
                    'host' => $this->config->host,
                    'port' => $this->config->port,
                    'username' => $this->config->username,
                    'password' => $this->config->password,
                    'db_name' => $this->config->name,
                ],
            ],
            'log_table_name' => $this->config->prefix . 'migration',
            'migration_dirs' => [
                'migration' => $this->config->migration,
            ],
        ];
    }

    public function paginate(SelectQuery $query): Pagination
    {
        return new Pagination($this, $query);
    }

    /**
     * @param array<string, ?scalar> $map
     * @throws \PDOException
     */
    public function update(
        string $table,
        array $map,
        string $field_id,
        string|int|float|bool $id,
    ): void {
        $this->execute(
            $this->query
                ->update($this->prefix . $table, $map)
                ->where(field($field_id)->eq($id)),
        );
    }
}
