<?php

namespace App\Http\Controllers;

use App\Http\Requests\LetrasSearchRequest;
use App\Letras\LetrasRecord;
use App\Letras\LetrasSchema;
use App\Page;
use Illuminate\Http\Request;

class LetrasController extends RecordController
{
    protected $logGroup = 'letras';

    /**
     * Display a listing of the resource.
     *
     * @param LetrasSearchRequest $request
     * @param LetrasSchema        $schema
     *
     * @return \Illuminate\Http\Response
     */
    public function index(LetrasSearchRequest $request, LetrasSchema $schema)
    {
        if ($request->wantsJson()) {
            return $this->dataTablesResponse($request, $schema);
        }

        $introPage = Page::where('slug', '=', 'letras/intro')->first();
        $intro = $introPage ? $introPage->body : '';

        return response()->view('letras.index', [
            'schema' => $schema,

            'query' => $request->all(),
            'processedQuery' => $request->queryParts,
            'advancedSearch' => ($request->advanced === 'on'),

            'intro' => $intro,

            'defaultColumns' => [
                // Verket
                'forfatter',
                'tittel',
                'utgivelsesaar',

                // Oversettelsen
                'oversetter',
                'tittel2',
                'utgivelsesaar2',
            ],
            'order' => [
                ['key' => 'utgivelsesaar', 'direction' => 'desc'],
            ],

        ]);
    }

    public function autocomplete(Request $request, LetrasSchema $schema)
    {
        $fieldName = $request->get('field');
        $fields = $schema->keyed();
        if (!isset($fields[$fieldName])) {
            throw new \RuntimeException('Invalid field');
        }

        $term = $request->get('q') . '%';
        $data = [];

        switch ($fieldName) {
            default:
                if ($term == '%') {
                    // Preload request
                    $rows = \DB::table('letras')
                        ->select($fieldName)
                        ->distinct()
                        ->groupBy($fieldName)
                        ->get();
                } else {
                    $rows = \DB::table('letras')
                        ->select($fieldName)
                        ->distinct()
                        ->where($fieldName, 'ilike', $term)
                        ->groupBy($fieldName)
                        ->get();
                }
                foreach ($rows as $row) {
                    $data[] = [
                        'value' => $row->{$fieldName},
                    ];
                }
                break;
        }

        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected function updateOrCreate(Request $request, $id = null)
    {
        $record = $id === null ? new LetrasRecord() : LetrasRecord::findOrFail($id);

        $this->validate($request, [
//            'forfatter'     => 'required' . (is_null($id) ? '' : ',' . $id) . '|max:255',
//            'land'      => 'required',
//            'tittel'     => 'required',
//            'utgivelsesaar' => 'required',
//            'sjanger' => 'required',
//            'oversetter' => 'required',
//            'tittel2' => 'required',
//            'utgivelsessted' => 'required',
//            'utgivelsesaar2' => 'required',
//            'forlag' => 'required',
//            'foretterord' => 'required',
//            'spraak' => 'required',
        ]);

        $record->forfatter = $request->get('forfatter');
        $record->land = $request->get('land');
        $record->tittel = $request->get('tittel');
        $record->utgivelsesaar = $request->get('utgivelsesaar');
        $record->sjanger = $request->get('sjanger');
        $record->oversetter = $request->get('oversetter');
        $record->tittel2 = $request->get('tittel2');
        $record->utgivelsessted = $request->get('utgivelsessted');
        $record->utgivelsesaar2 = $request->get('utgivelsesaar2');
        $record->forlag = $request->get('forlag');
        $record->foretterord = $request->get('foretterord');
        $record->spraak = $request->get('spraak');

        $record->save();

        return $record;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param LetrasSchema $schema
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function create(LetrasSchema $schema)
    {
        $this->authorize('letras');

        $data = $this->formArguments(new LetrasRecord(), $schema);

        return response()->view('letras.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('letras');

        $record = $this->updateOrCreate($request);

        $this->log(
            'Opprettet <a href="%s">post #%s (%s)</a>.',
            action('LetrasController@show', $record->id),
            $record->id,
            $record->tittel
        );

        return redirect()->action('LetrasController@show', $record->id)
            ->with('status', 'Posten ble opprettet.');
    }

    public function show(LetrasSchema $schema, $id)
    {
        $record = LetrasRecord::findOrFail($id);

        $data = [
            'schema' => $schema,
            'record'  => $record,
        ];

        return response()->view('letras.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param LetrasSchema $schema
     * @param int          $id
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(LetrasSchema $schema, $id)
    {
        $this->authorize('letras');

        $record = LetrasRecord::findOrFail($id);

        $data = $this->formArguments($record, $schema);

        return response()->view('letras.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize('letras');

        $record = $this->updateOrCreate($request, $id);

        $this->log(
            'Oppdaterte <a href="%s">post #%s (%s)</a>.',
            action('LetrasController@show', $record->id),
            $record->id,
            $record->tittel
        );

        return redirect()->action('LetrasController@show', $id)
            ->with('status', 'Posten ble lagret');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('letras');

        //
    }
}
