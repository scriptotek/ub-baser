@extends('litteraturkritikk.layout')

@section('content')

    <h2>Rediger verk {{ $record->id }}</h2>

    @include('shared.errors')

    <edit-form
        method="PUT"
        action="{{ $base->action('WorkController@update', $record->id) }}"
        csrf-token="{{ csrf_token() }}"
        :schema="{{ json_encode($schema) }}"
        :settings="{{ json_encode($settings) }}"
        :values="{{ json_encode($values) }}"
    ></edit-form>

@endsection