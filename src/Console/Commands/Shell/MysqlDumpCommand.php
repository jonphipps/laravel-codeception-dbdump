<?php

namespace Antennaio\Codeception\Console\Commands\Shell;

class MysqlDumpCommand implements DumpCommand
{
    /**
     * Execute shell command.
     *
     * @param string $dump
     * @param string $database
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $binary
     *
     * @return bool
     */
    public function execute($dump, $database, $host = null, $username = null, $password = null, $binary = null)
    {
        $binary = is_null($binary) ? 'mysqldump' : $binary;
        $command = "$binary -h $host";
        $command .= " -u $username";
        $command .= ($password) ? " -p$password" : '';
        $command .= " -c $database";
        $command .= " > $dump";

        exec($command, $output, $status);

        return $status == 0;
    }
}
