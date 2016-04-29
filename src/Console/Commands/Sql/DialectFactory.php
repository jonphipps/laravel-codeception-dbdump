<?php

namespace Antennaio\Codeception\Console\Commands\Sql;

class DialectFactory
{
    /**
     * Return dialect.
     *
     * @param string $dialect
     *
     * @return Dialect
     */
    public static function create($dialect)
    {
        switch ($dialect) {
            case 'mysql':
                return new MysqlDialect();
            case 'sqlite':
                return new SqliteDialect();
            default:
                throw new \Exception('This SQL dialect is not supported: '.$dialect);
        }
    }
}
