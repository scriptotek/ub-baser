<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeBaseCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:base';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new base';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Base';

    protected $appStubs = [
        'Record',
        'Controller',
        'Schema',
        'AutocompleteService',
        'Config',
    ];

    protected $viewStubs = [
        'index',
        'show',
        'create',
        'edit',
        'layout',
    ];

    /**
     * @inheritDoc
     */
    protected function getStub()
    {
        return null;
    }

    /**
     * @param string $filename
     * @param string $classname
     * @param string $basename
     * @return string
     */
    protected function getNamedStub(string $filename, string $classname = null, string $basename = null)
    {
        $filename = __DIR__ . '/stubs/' . $filename. '.php.stub';
        $stub = $this->files->get($filename);
        if (!is_null($classname)) {
            $stub = $this->replaceNamespace($stub, $classname)->replaceClass($stub, $classname);
        }
        if (!is_null($basename)) {
            $stub = str_replace('dummy', $basename, $stub);
        }

        return $stub;
    }

    /**
     * Get the full path to the migration.
     *
     * @param  string  $name
     * @param  string  $path
     * @return string
     */
    protected function getMigrationPath($name, $path)
    {
        return $path.'/'.$this->getDatePrefix().'_'.$name.'.php';
    }

    /**
     * Get the date prefix for the migration.
     *
     * @return string
     */
    protected function getDatePrefix()
    {
        return date('Y_m_d_His');
    }

    protected function makeAppClasses($basename)
    {
        // Tha app namespace contains the app classes.
        // Example: 'Bases/Epitafier'
        $namespace = 'Bases/' . Str::studly($basename);

        // The app path is the path to the app classes.
        // Example: 'app/Bases/Epitafier'
        $appPath = app_path($namespace);

        $this->info("Creating directory: $appPath");

        $this->makeDirectory($appPath . '/Dummy');

        foreach ($this->appStubs as $stubname) {
            $classname = $this->qualifyClass($namespace . '\\' . $stubname);
            $this->files->put(
                $this->getPath($classname),
                $this->getNamedStub($stubname, $classname, $basename)
            );
            $this->info("Created $classname");
        }
    }

    protected function makeMigration(string $basename)
    {
        $migrationDir = app()->databasePath('migrations');
        $create = 'create_' . $basename . '_table';
        $classname = Str::studly($create);
        $this->files->put(
            $this->getMigrationPath($create, $migrationDir),
            $this->getNamedStub('create_table', $classname, $basename)
        );
        $this->info("Created $classname");
    }

    protected function makeViews(string $basename)
    {
        $resPath = resource_path('views/' . $basename);
        $this->makeDirectory($resPath . '/Dummy');

        foreach ($this->viewStubs as $stubname) {
            $filename = $stubname . '.blade';
            $this->files->put(
                $resPath . '/' . $filename . '.php',
                $this->getNamedStub($filename)
            );
            $this->info("Created $filename");
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // The basename is used as table name and primary identifier of the base.
        // Example: 'epitafier'
        $basename = Str::lower($this->getNameInput());

        // Generate the app classes
        $this->makeAppClasses($basename);

        // Generate migration
        $this->makeMigration($basename);

        // Generate views
        $this->makeViews($basename);
    }
}
