<?php

namespace Antennaio\Codeception\Test;

use Antennaio\Codeception\DbDumpServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            DbDumpServiceProvider::class
        ];
    }
}
