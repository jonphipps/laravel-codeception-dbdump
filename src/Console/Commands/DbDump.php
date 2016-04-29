<?php

namespace Antennaio\Codeception\Console\Commands;

use Antennaio\Codeception\Console\Commands\Sql\Dialect;
use Antennaio\Codeception\Console\Commands\Sql\DialectFactory;
use Antennaio\Codeception\Console\Commands\Shell\DumpCommandFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DbDump extends Command
{
    /**
     * @var string
     */
    protected $connection;

    /**
     * @var array
     */
    protected $config = [
        'driver' => null,
        'database' => null,
        'host' => null,
        'username' => null,
        'password' => null,
    ];

    /**
     * @var Dialect
     */
    protected $sqlDialect;

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
     * Perform setup.
     */
    private function setup()
    {
        $this->connection = $this->argument('connection');
        $this->config = array_merge($this->config, Config::get('database.connections.'.$this->connection));
        $this->sqlDialect = DialectFactory::create($this->config['driver']);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->setup();

        $this->emptyDatabase();
        $this->migrate();
        $this->seed();
        $this->dump();
    }

    /**
     * Delete all database tables
     */
    private function emptyDatabase()
    {
        if ($this->option('empty-database')) {
            $this->info("Truncating $this->connection database.");

            $tableNames = Schema::connection($this->connection)
                ->getConnection()
                ->getDoctrineSchemaManager()
                ->listTableNames();

            $this->sqlDialect->setForeignKeyChecks(false);

            foreach ($tableNames as $table) {
                Schema::connection($this->connection)->drop($table);
            }

            $this->sqlDialect->setForeignKeyChecks(true);
        }
    }

    /**
     * Migrate test database.
     */
    private function migrate()
    {
        $this->info("Migrating $this->connection database.");

        $this->call('migrate', ['--database' => $this->connection]);
    }

    /**
     * Seed test database.
     */
    private function seed()
    {
        if (!$this->option('no-seed')) {
            $this->info("Seeding $this->connection database.");

            $opts = ['--database' => $this->connection];

            if ($this->option('seed-class')) {
                $opts['--class'] = $this->option('seed-class');
            }

            $this->call('db:seed', $opts);
        }
    }

    /**
     * Dump test database.
     */
    private function dump()
    {
        try {
            $command = DumpCommandFactory::create($this->config['driver']);
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
            exit();
        }

        $success = $command->execute(
            $this->option('dump'),
            $this->config['database'],
            $this->config['host'],
            $this->config['username'],
            $this->config['password'],
            $this->option('binary-dump')
        );

        if ($success) {
            $this->info('Database dump created successfully.');
        } else {
            $this->error('Something went wrong when creating database dump!');
        }
    }
}
