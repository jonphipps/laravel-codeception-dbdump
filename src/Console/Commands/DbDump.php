<?php

namespace Antennaio\Codeception\Console\Commands;

use Antennaio\Codeception\Console\Commands\Shell\DumpCommandFactory;
use Artisan;
use Illuminate\Console\Command;

class DbDump extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'codeception:dbdump {connection}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate, seed and create an SQL dump of a test database';

    /**
     * The path where the dump will be stored.
     *
     * @var string
     */
    protected $dump = 'tests/_data/dump.sql';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $connection = $this->argument('connection');

        $this->migrate($connection);
        $this->seed($connection);
        $this->dump($connection);
    }

    /**
     * Migrate test database.
     *
     * @param string $connection
     * @return void
     */
    private function migrate($connection)
    {
        $this->info('Migrating test database.');

        Artisan::call('migrate', ['--database' => $connection]);
    }

    /**
     * Seed test database.
     *
     * @param string $connection
     * @return void
     */
    private function seed($connection)
    {
        $this->info('Seeding test database.');

        Artisan::call('db:seed', ['--database' => $connection]);
    }

    /**
     * Dump test database.
     *
     * @param string $connection
     * @return void
     */
    private function dump($connection)
    {
        $driver = config('database.connections.'.$connection.'.driver');
        $database = config('database.connections.'.$connection.'.database');
        $host = config('database.connections.'.$connection.'.host');
        $username = config('database.connections.'.$connection.'.username');
        $password = config('database.connections.'.$connection.'.password');

        try {
            $command = DumpCommandFactory::create($driver);
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
            exit();
        }

        $success = $command->execute(
            $this->dump,
            $database,
            $host,
            $username,
            $password
        );

        if ($success) {
            $this->info('Database dump created successfully.');
        } else {
            $this->error('Something went wrong when creating database dump!');
        }
    }
}
