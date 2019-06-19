<?php

namespace Vkovic\LaravelCommandos\Console\Commands\Database;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Vkovic\LaravelCommandos\Handlers\Database\Exceptions\AbstractDbException;
use Vkovic\LaravelCommandos\Handlers\Database\MySql;
use Vkovic\LaravelCommandos\Handlers\Messages;

class DbDrop extends Command
{
    use ConfirmableTrait;

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
    protected $signature = 'db:drop
                               {database? : Database (name) to be created. If passed env DB_DATABASE will be ignored} 
                           ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop database';

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

        $this->info("Dropping database: '$database'");

        try {
            $dbHandler->dropDatabase($database);
        } catch (AbstractDbException $e) {
            return  $this->line($e->getMessage());
        }

        $this->info('Done');
    }
}
