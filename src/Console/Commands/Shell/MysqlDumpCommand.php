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
   * @param null $port
   *
   * @return bool
   */
    public function execute($dump, $database, $host = null, $username = null, $password = null, $binary = null, $port = null)
    {
        $binary = is_null($binary) ? 'mysqldump' : $binary;
        $port = is_null($port) ? '' : "-P " . $port;

        $command = "$binary -h $host $port";
        $command .= " -u $username";
        $command .= ($password) ? " -p$password" : '';
        $command .= " -c $database";
        $command .= " > $dump";

        exec($command, $output, $status);

        return $status == 0;
    }
}
