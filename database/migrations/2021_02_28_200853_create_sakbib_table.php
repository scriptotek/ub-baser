<?php

use App\Traits\MigrationHelper;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSakbibTable extends Migration
{
    use MigrationHelper;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sb_publication', function (Blueprint $table) {
            $this->addCommonFields($table);
            $table->integer('user_id')->unsigned();

            $table->text('year')->nullable();
            $table->text('title')->nullable();
            $table->text('bibsys_objektid')->nullable();
            $table->text('report_type')->nullable();
            $table->text('pub_type')->nullable();
            $table->text('series')->nullable();
            $table->text('volume')->nullable();
            $table->text('publisher')->nullable();
            $table->text('publication_place')->nullable();
            $table->text('issn')->nullable();
            $table->text('isbn')->nullable();
            $table->text('journal')->nullable();
            $table->text('booktitle')->nullable();
            $table->text('number')->nullable();
            $table->text('chapter')->nullable();
            $table->text('edition')->nullable();
            $table->text('school')->nullable();
            $table->text('note')->nullable();
            $table->text('abstract')->nullable();
            $table->text('url')->nullable();
            $table->text('doi')->nullable();
            $table->text('crossref')->nullable();
            $table->text('pages')->nullable();

            $table->text('public_note')->nullable();
            $table->text('private_note')->nullable();
            $table->json('keywords')->nullable();

            $table->boolean('public')->default(true);
        });

        Schema::create('sb_creator', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name')->nullable();
            $table->text('entity_type')->nullable();
            $table->text('noraf_id')->nullable();
            $table->softDeletes();
        });

        Schema::create('sb_creator_publication', function (Blueprint $table) {
            $table->integer('creator_id')->unsigned();
            $table->integer('publication_id')->unsigned();
            $table->integer('posisjon')->unsigned();
            $table->json('role');
        });

        Schema::create('sb_category', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name')->nullable();
            $table->text('description')->nullable();
            $table->integer('parent_category_id')->unsigned()->nullable();
            $table->softDeletes();
        });

        Schema::create('sb_category_publication', function (Blueprint $table) {
            $table->integer('category_id')->unsigned();
            $table->integer('publication_id')->unsigned();
        });

        $this->createView('sb_view', 1);

        \DB::table('bases')->insert([
            [
                'id' => 'sakbib',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'basepath' => 'sakbib',
                'namespace' => 'Sakbib',
                'languages' => json_encode(['nb']),
                'default_language' => 'nb',
                'name' => json_encode([
                    'nb' => 'Sakbib',
                ]),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropMaterializedView('sb_view');

        Schema::dropIfExists('sb_category_publication');
        Schema::dropIfExists('sb_category');
        Schema::dropIfExists('sb_creator_publication');
        Schema::dropIfExists('sb_creator');
        Schema::dropIfExists('sb_publication');

        \DB::table('bases')
            ->where(['id' => 'sakbib'])
            ->delete();
    }
}
