@extends('layout')

@section('db-title', 'Sakbib')

@section('h1-title')
    <a href="{{ $base->action('index') }}">Sakbib</a>
@endsection

@section('footer-column1')
    <ul class="list-unstyled">
        <li>
            Kontaktperson:<br>
            <a href="https://www.ub.uio.no/om/ansatte/uhs/uhsfagstudier/ang/index.html">Anne Sæbø?</a>
        </li>
    </ul>
@endsection
