<?php

namespace Antennaio\Codeception\Console\Commands\Shell;

class SqliteDumpCommand implements DumpCommand
{
    /**
     * Execute shell command.
     *
     * @param string $dump
     * @param string $database
     * @param string $host
     * @param string $username
     * @param string $password
     * @return boolean
     */
    public function execute($dump, $database, $host = null, $username = null, $password = null)
    {
        $command = "sqlite3 $database '.dump'";
        $command .= " > $dump";

        exec($command, $output, $status);

        return $status == 0;
    }
}
