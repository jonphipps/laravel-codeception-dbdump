<?php

namespace Antennaio\Codeception\Console\Commands;

use Antennaio\Codeception\Console\Commands\Shell\DumpCommandFactory;
use Illuminate\Console\Command;

class DbDump extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'codeception:dbdump
        {connection : Focus the database connection,from app/database.php, you want to dump}
        {--dump=tests/_data/dump.sql : Choose the path for your dump file}
        {--empty-database : Delete all database tables before any other action}
        {--no-seed : Disable the seed in the dump process}
        {--seed-class=DatabaseSeeder : Choose the class to seed in your dump (class from database/seeds)}
        {--binary-dump= : Specify the path to mysqldump (only for mysql connection driver) or sqlite3 (only for sqlite connection driver) to make the dump}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate, seed and create an SQL dump of a test database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $connection = $this->argument('connection');

        $this->emptyDatabase($connection);
        $this->migrate($connection);
        $this->seed($connection);
        $this->dump($connection);
    }

    private function emptyDatabase($connection)
    {
        if ($this->option('empty-database'))
        {
            $this->info("Truncating $connection database.");

            $tableNames = \Schema::connection($connection)
                ->getConnection()
                ->getDoctrineSchemaManager()
                ->listTableNames();

            foreach ($tableNames as $name) {
                \DB::connection($connection)
                    ->table($name)
                    ->delete();
            }
        }
    }

    /**
     * Migrate test database.
     *
     * @param string $connection
     */
    private function migrate($connection)
    {
        $this->info("Migrating $connection database.");
        $this->call('migrate', ['--database' => $connection]);
    }

    /**
     * Seed test database.
     *
     * @param string $connection
     */
    private function seed($connection)
    {
        if (!$this->option('no-seed')) {
            $this->info("Seeding $connection database.");

            $opts = ['--database' => $connection];

            if ($this->option('seed-class')) {
                $opts['--class'] = $this->option('seed-class');
            }

            $this->call('db:seed', $opts);
        }
    }

    /**
     * Dump test database.
     *
     * @param string $connection
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
            $this->option('dump'),
            $database,
            $host,
            $username,
            $password,
            $this->option('binary-dump')
        );

        if ($success) {
            $this->info('Database dump created successfully.');
        } else {
            $this->error('Something went wrong when creating database dump!');
        }
    }
}
