@extends('sakbib.layout')

@section('content')

    <h2>Slett kategori {{ $record->id }}</h2>

    @include('shared.errors')

    <div class="panel panel-default">

        <div class="panel-body">

            <p>
                Merk: Ved sletting av en forfatter blir alle tilknyttede poster frakoblet kategorien.
                Postene selv blir ikke slettet.
            </p>

            <form method="POST" action="{{ $base->action('CreatorController@destroy', $record->id) }}">
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="DELETE">

                <div class="form-group row">
                    <div class="col-sm-12">
                        <label>
                            <input type="checkbox" name="confirm-deletecategory">
                            {{ trans('sakbib.confirm-deletecategory') }}
                        </label>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-danger">{{ trans('sakbib.deletecategory') }}</button>
                    </div>
                </div>

            </form>

        </div>
    </div>

@endsection
