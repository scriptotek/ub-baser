<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Password;
use Symfony\Component\Process\Process;

class DatabaseDumpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:dump
                            {--database= : Name of database in config file}
                            {--path=     : Path to destination file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dump database for codeception testing';

    // protected function getDumpCommand()
    // {
    //     return config('backup::mysql.dump_command_path', 'mysqldump');
    // }

    public function getMysqlCommand($config, $destinationFile)
    {
        $dumpCommand = 'mysqldump';

        return sprintf('%s --user=%s --password=%s --host=%s --port=%s %s > %s',
            $dumpCommand,
            escapeshellarg($config['username']),
            escapeshellarg($config['password']),
            escapeshellarg($config['host']),
            escapeshellarg(array_get($config, 'port', 3306)),
            escapeshellarg($config['database']),
            escapeshellarg($destinationFile)
        );
    }

    public function getPostgresCommand($config, $destinationFile)
    {
        $dumpCommand = 'pg_dump';

        return sprintf('PGPASSWORD=%s %s -Fc --no-acl --no-owner --format=plain -h %s -U %s %s > %s',
            escapeshellarg($config['password']),
            $dumpCommand,
            escapeshellarg($config['host']),
            escapeshellarg($config['username']),
            escapeshellarg($config['database']),
            escapeshellarg($destinationFile)
        );
    }

    public function getCommand($destinationFile)
    {
        $conn = $this->option('database') ?: config('database.default');
        $config = config('database.connections.' . $conn);

        switch ($config['driver']) {
            case 'mysql':
                return $this->getMysqlCommand($config, $destinationFile);
                break;
            case 'pgsql':
                return $this->getPostgresCommand($config, $destinationFile);
                break;
            default:
                throw new \Exception('Unsupported database: ' . $conn);
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $destinationFile = $this->option('path') ?: base_path('tests/_data/dump.sql');

        $command = $this->getCommand($destinationFile);

        $process = new Process($command);
        $process->setTimeout(60);
        $process->run();
        if ($process->isSuccessful()) {
            $this->info('Database dumped successfully to ' . $destinationFile);
        } else {
            $this->error('Could not dump database to ' . $destinationFile);
            $this->error($process->getErrorOutput());
        }
    }
}