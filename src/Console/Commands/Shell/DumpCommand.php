<?php

namespace Antennaio\Codeception\Console\Commands\Shell;

interface DumpCommand
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
    public function execute($dump, $database, $host = null, $username = null, $password = null, $binary = null);
}
