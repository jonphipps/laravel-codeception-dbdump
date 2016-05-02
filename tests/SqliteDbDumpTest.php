<?php

namespace Antennaio\Codeception\Test;

use Antennaio\Codeception\Console\Commands\DbDump;
use Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class SqliteDbDumpTest extends TestCase
{
    public function testDbDump()
    {
        Config::set('database.connections.sqlite_testing', [
            'driver'   => 'sqlite',
            'database' => __DIR__ . '/db/database.sqlite',
        ]);

        $this->artisan('codeception:dbdump', [
            'connection' => 'sqlite_testing',
            '--dump' => __DIR__ . '/db/database-dump.sql',
            '--no-seeding' => true,
            '--no-interaction' => true,
        ]);

        $this->assertTrue(File::exists(__DIR__ . '/db/database-dump.sql'));

        $this->assertEquals(strpos(File::get(__DIR__ . '/db/database-dump.sql'), 'BEGIN TRANSACTION;'), 25);
        $this->assertEquals(strpos(File::get(__DIR__ . '/db/database-dump.sql'), 'CREATE TABLE "users"'), 128);
        $this->assertEquals(strpos(File::get(__DIR__ . '/db/database-dump.sql'), 'COMMIT;'), 699);
    }

    public function tearDown()
    {
        parent::tearDown();

        File::delete(__DIR__ . '/db/database-dump.sql');
    }
}
