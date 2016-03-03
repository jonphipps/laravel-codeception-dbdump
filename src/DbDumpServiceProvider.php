<?php

namespace Antennaio\Codeception;

use Antennaio\Codeception\Console\Commands\DbDump;
use Illuminate\Support\ServiceProvider;

class DbDumpServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            DbDump::class
        ]);
    }
}
