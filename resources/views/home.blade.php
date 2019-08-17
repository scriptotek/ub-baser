@extends('layouts.master')

@section('db-title', 'UB-baser')

@section('content')

        <ul id="database_list">
            <li>
                <a href="{{ action('LitteraturkritikkController@index') }}">Norsk litteraturkritikk</a>
            </li>
            <li>
                <a href="{{ action('OpesController@index') }}">OPES</a>
            </li>
            <li>
                <a href="{{ action('LetrasController@index') }}">Letras</a>
            </li>
            <li>
                <a href="{{ action('DommerController@index') }}">Dommers populærnavn</a>
            </li>
        </ul>

@endsection
