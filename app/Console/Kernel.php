<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\CreateUserCommand::class,
        \App\Console\Commands\CreateAdminCommand::class,
        \App\Console\Commands\DatabaseDumpCommand::class,
        \App\Console\Commands\ImportLitteraturkritikkCommand::class,
        \App\Console\Commands\ImportDommerCommand::class,
        \App\Console\Commands\ImportLetrasCommand::class,
        \App\Console\Commands\ImportBibliomanuelCommand::class,
        \App\Console\Commands\ImportBibsysCommand::class,
        \App\Console\Commands\ImportOpesCommand::class,
        \App\Console\Commands\PurgeLogs::class,
        \App\Console\Commands\Deployed::class,
        \App\Console\Commands\PurgeLocks::class,
        \App\Console\Commands\MakeBaseCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(\App\Console\Commands\PurgeLocks::class)->hourly();
        $schedule->command(\App\Console\Commands\PurgeLogs::class)->daily();
    }
}
