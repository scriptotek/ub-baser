<?php

namespace App\Console\Commands;

class ImportSakbibCommand extends ImportCommand
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'import:sakbib';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data for "Sakbib"';

    /**
     * Import file format.
     *
     * @var string
     */
    protected $fileFormat = 'csv';

    /**
     * Tables to import.
     *
     * @var string[]
     */
    protected $tables = [
        'sb_publication',
        'sb_creator',
        'sb_creator_publication',
        'sb_category',
        'sb_category_publication',
    ];

    /**
     * Sequences to update
     *
     * @var string[]
     */
    protected $sequences = [
        'sb_creator.id',
        'sb_publication.id',
        'sb_category.id',
    ];

    /**
     * Views to refresh.
     *
     * @var string[]
     */
    protected $views = [
        'sb_view',
    ];
}
