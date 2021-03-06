<?php

use App\Traits\MigrationHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDommerTable extends Migration
{
    use MigrationHelper;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dommer_kilder', function (Blueprint $table) {
            $table->increments('id');
            $table->string('navn');
        });

        Schema::create('dommer', function (Blueprint $table) {
            $this->addCommonFields($table);

            $table->string('navn');
            $table->integer('aar')->unsigned();
            $table->integer('kilde_id')->unsigned();
            $table->integer('side')->unsigned();
            $table->string('note')->nullable();

            $table->index('navn');
            $table->index('aar');
            $table->index('side');

            $table->unique(['navn', 'kilde_id', 'aar', 'side']);

            $table->foreign('kilde_id')
                ->references('id')->on('dommer_kilder');
        });

        $this->createView('dommer_view', 1);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('dommer_view');
        Schema::drop('dommer');
        Schema::drop('dommer_kilder');
    }
}
