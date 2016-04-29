<?php

namespace Antennaio\Codeception\Console\Commands\Sql;

class MysqlDialect implements Dialect
{
    /**
     * Set foreign key checks on/off.
     *
     * @param bool $state
     *
     * @return string
     */
    public function setForeignKeyChecks($state)
    {
        $flag = (int) $state;

        return "SET foreign_key_checks = $flag";
    }
}
