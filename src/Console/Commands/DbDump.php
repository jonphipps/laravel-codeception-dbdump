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
        {connection : Specify the database connection, it needs to be one of the connections listed in config/database.php}
        {--dump=tests/_data/dump.sql : Choose the path for your dump file}
        {--empty-database : Delete all database tables before any other action}
        {--no-seeding : Disable seeding in the dump process}
        {--seeder-class=DatabaseSeeder : Choose the seeder class}
        {--binary= : Specify the path to mysqldump (if using mysql driver) or sqlite3 (if using sqlite driver)}';

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
        if (!$this->option('empty-database')) {
            return;
        }

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
        if ($this->option('no-seeding')) {
            return;
        }

        $this->info("Seeding $this->connection database.");

        $opts = ['--database' => $this->connection];

        if ($this->option('seeder-class')) {
            $opts['--class'] = $this->option('seeder-class');
        }

        $this->call('db:seed', $opts);
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
            $this->option('binary')
        );

        if ($success) {
            $this->info('Database dump created successfully.');
        } else {
            $this->error('Something went wrong when creating database dump!');
        }
    }
}
