<?php

namespace App\Http\Controllers;


use App\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pages = Page::query();

        $data = [
            'records'       => $records->paginate(200),
        ];

        return response()->view('pages.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Page $page
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Page $page)
    {
        $data = [
            'editRoute' => $page->name . '.edit',
            'page'      => $page,
        ];

        return response()->view('pages.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Page $page
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Page $page)
    {
        $this->authorize($page->permission);

        $data = [
            'updateRoute' => $page->name . '.update',
            'page'        => $page,
        ];

        return response()->view('pages.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Page                $page
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Page $page)
    {
        $this->authorize($page->permission);

        $page->body = $request->body;
        $page->updated_by = $request->user()->id;
        $page->save();

        return redirect()->route($page->name)
            ->with('status', 'Siden ble lagret.');
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
        //
    }
}