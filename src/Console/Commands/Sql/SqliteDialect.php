<?php

namespace Antennaio\Codeception\Console\Commands\Sql;

class SqliteDialect implements Dialect
{
    /**
     * Set foreign key checks on/off.
     *
     * @param boolean $state
     *
     * @return string
     */
    public function setForeignKeyChecks($state)
    {
        $flag = ($state) ? 'ON' : 'OFF';

        return "PRAGMA foreign_keys = $flag";
    }
}
