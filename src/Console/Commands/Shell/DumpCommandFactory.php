<?php

namespace Antennaio\Codeception\Console\Commands\Shell;

class DumpCommandFactory
{
    /**
     * Return command.
     *
     * @param string $driver
     *
     * @return DumpCommand
     */
    public static function create($driver)
    {
        switch ($driver) {
            case 'mysql':
                return new MysqlDumpCommand();
            case 'sqlite':
                return new SqliteDumpCommand();
            default:
                throw new \Exception('This driver is not supported: '.$driver);
        }
    }
}
