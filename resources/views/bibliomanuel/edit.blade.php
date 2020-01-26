@extends('bibliomanuel.layout')

@section('content')

    <h2>
        Rediger post #{{ $record->id }}
    </h2>

    @include('shared.errors')

    <form method="POST" action="{{ $base->action('update', $record->id) }}" class="form-horizontal">
        {!! csrf_field() !!}
        <input type="hidden" name="_method" value="PUT">

        <edit-form
            :schema="{{ json_encode($schema) }}"
            :settings="{{ json_encode($settings) }}"
            :values="{{ json_encode($values) }}"
        ></edit-form>

        <div class="form-group">
            <div class="col-sm-10">
                <button type="submit" class="btn btn-primary">{{ trans('messages.update') }}</button>
            </div>
        </div>

    </form>

@endsection