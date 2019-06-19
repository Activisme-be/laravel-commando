<?php

namespace Vkovic\LaravelCommandos\Console\Commands\Database;

use Illuminate\Console\Command;
use Vkovic\LaravelCommandos\Handlers\Database\Exceptions\AbstractDbException;
use Vkovic\LaravelCommandos\Handlers\Database\MySql;

class DbCreate extends Command
{
    /**
     * Current database name (from env)
     *
     * @var string
     */
    protected $db;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create
                                {database? : Database (name) to be created. If passed env DB_DATABASE will be ignored.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create db defined in .env file or with custom name if argument passed';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get database name either from passed argument (if any)
        // or from default database configuration
        $database = $this->argument('database') ?: (function () {
            $default = config('database.default');

            return config("database.connections.$default.database");
        })();

        $config = config()->get('database.connections.mysql');
        $dbHandler = new MySql($config);

        $this->info("Creating database: '$database'");

        try {
            $dbHandler->createDatabase($database);
        } catch (AbstractDbException $e) {
            return $this->error($e->getMessage());
        }

        $this->info('Done');
    }
}
