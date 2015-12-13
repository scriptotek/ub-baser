<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLitteraturkritikkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('litteraturkritikk_kritikktyper', function (Blueprint $table) {
            $table->increments('id');
            $table->string('navn');
        });

        Schema::create('litteraturkritikk', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            # Om kritikken
            $table->jsonb('kritikktype');
            $table->string('kommentar')->nullable();  # Om innholdet i kritikken. Hvorvidt publisert flere steder, f.eks. ”jfr.” eller ”også”.
            $table->string('spraak')->nullable();     # Språk, kritikk

            # Om utgivelsen av kritikken
            $table->string('tittel')->nullable();
            $table->text('publikasjon')->nullable();   # > 250 chars
            $table->string('utgivelsessted')->nullable();
            $table->string('aar')->nullable();
            $table->string('dato')->nullable();
            $table->string('aargang')->nullable();
            $table->string('nummer')->nullable();
            $table->string('bind')->nullable();
            $table->string('hefte')->nullable();
            $table->string('sidetall')->nullable();
            $table->string('utgivelseskommentar')->nullable();   # Eventuelle tilleggsopplysninger om publiseringen. F.eks. ”egenpublisert”

            # Om kritikeren
            $table->string('kritiker_etternavn')->nullable();
            $table->string('kritiker_fornavn')->nullable();
            $table->string('kritiker_kjonn')->nullable();
            $table->string('kritiker_pseudonym')->nullable();
            $table->string('kritiker_kommentar')->nullable();

            # Om det kritiserte verket og dets forfatter
            $table->string('forfatter_etternavn')->nullable();
            $table->string('forfatter_fornavn')->nullable();
            $table->string('forfatter_kjonn')->nullable();
            $table->string('forfatter_kommentar')->nullable();  # F.eks. navn på evt. medforfatter(e)), pseudonym m.m.
            $table->string('verk_tittel')->nullable();     # "Verkstittel", tittel på verket som kritiseres
            $table->string('verk_aar')->nullable();        # "Utgivelsesår", i hvilket år er verket utgitt?
            $table->string('verk_sjanger')->nullable();    # "Sjanger", hvilken sjanger tilhører verket?
            $table->jsonb('verk_spraak')->nullable();
            $table->string('verk_kommentar')->nullable();  # F.eks. undertitler, forlag, opplag, omarbeidet utgave m.m.
            $table->string('verk_utgivelsessted')->nullable();

            $table->index('forfatter_etternavn');
            $table->index('forfatter_fornavn');
            $table->index('verk_tittel');
            $table->index('kritiker_etternavn');
            $table->index('kritiker_fornavn');
            $table->index('kritiker_pseudonym');
            $table->index('publikasjon');
        });

        // Add column for search index
        DB::unprepared('
            ALTER TABLE litteraturkritikk ADD COLUMN tsv tsvector;
            CREATE INDEX litteraturkritikk_tsv_idx ON litteraturkritikk USING gin(tsv);
            CREATE TRIGGER update_tsv BEFORE INSERT OR UPDATE
                ON litteraturkritikk FOR EACH ROW EXECUTE PROCEDURE
                tsvector_update_trigger(tsv, "pg_catalog.simple", verk_tittel, tittel, forfatter_etternavn, forfatter_fornavn, kritiker_etternavn, kritiker_fornavn);

            ALTER TABLE litteraturkritikk ADD COLUMN tsv_person tsvector;
            CREATE INDEX litteraturkritikk_tsv_person_idx ON litteraturkritikk USING gin(tsv_person);
            CREATE TRIGGER update_tsv_person BEFORE INSERT OR UPDATE
                ON litteraturkritikk FOR EACH ROW EXECUTE PROCEDURE
                tsvector_update_trigger(tsv_person, "pg_catalog.simple", forfatter_etternavn, forfatter_fornavn, kritiker_etternavn, kritiker_fornavn);
        ');

        DB::unprepared("
            CREATE VIEW litteraturkritikk_view AS
                SELECT
                    litteraturkritikk.*,
                    (litteraturkritikk.forfatter_fornavn::text || ' '::text || litteraturkritikk.forfatter_etternavn::text) AS forfatter_fornavn_etternavn,
                    (litteraturkritikk.forfatter_etternavn::text || ' '::text || litteraturkritikk.forfatter_fornavn::text) AS forfatter_etternavn_fornavn,
                    (litteraturkritikk.kritiker_fornavn::text || ' '::text || litteraturkritikk.kritiker_etternavn::text) AS kritiker_fornavn_etternavn,
                    (litteraturkritikk.kritiker_etternavn::text || ' '::text || litteraturkritikk.kritiker_fornavn::text) AS kritiker_etternavn_fornavn,
                    substr(trim(aar),1,4) AS aar_numeric
                FROM litteraturkritikk;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER update_tsv_person ON litteraturkritikk');
        DB::unprepared('DROP INDEX litteraturkritikk_tsv_person_idx');
        DB::unprepared('DROP TRIGGER update_tsv ON litteraturkritikk');
        DB::unprepared('DROP INDEX litteraturkritikk_tsv_idx');
        DB::unprepared('DROP VIEW litteraturkritikk_view');
        Schema::drop('litteraturkritikk');
        Schema::drop('litteraturkritikk_kritikktyper');
    }
}