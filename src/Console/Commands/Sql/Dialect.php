<?php

namespace Antennaio\Codeception\Console\Commands\Sql;

interface Dialect
{
    /**
     * Set foreign key checks on/off.
     *
     * @param boolean $state
     *
     * @return string
     */
    public function setForeignKeyChecks($state);
}
