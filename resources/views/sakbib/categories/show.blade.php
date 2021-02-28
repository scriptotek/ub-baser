@extends('sakbib.layout')

@section('content')

    <p>
        @can('sakbib')
            <a href="{{ $base->action('CategoryController@edit', $record->id) }}" class="btn btn-outline-primary">
                <em class="fa fa-edit"></em>
                Rediger kategori
            </a>
            <a href="{{ $base->action('CategoryController@delete', $record->id) }}" class="btn btn-outline-danger">
                <em class="fa fa-trash"></em>
                Slett kategori
            </a>
        @endcan
    </p>

    <h2>
        {{ strval($record) }}
    </h2>

    @if ( $record->trashed() )
        <div class="alert alert-danger">
            Denne kategorien er slettet.
        </div>
    @endif

    <dl class="row">
        @foreach ($schema->fields as $field)
            @if ($field->showInRecordView && !empty($record->{$field->key}))
                <dt class="col-sm-3 text-sm-right">
                    {{ trans('sakbib.creator.' . $field->key) }}:
                </dt>
                <dd class="col-sm-9">
                    @if (method_exists($field, 'formatValue'))
                        {!! $field->formatValue($record->{$field->key}, $base) !!}
                    @else
                        {{ $record->{$field->key} }}
                    @endif
                </dd>
            @endif
        @endforeach
    </dl>

    @if (count($record->publications))
        <div id="publications">
            <h3>Kategori brukt på {{ count($record->publications ) }} {{ count($record->publications ) == 1 ? 'verk' : 'verk' }}</h3>
            <ul>
                @foreach ($record->publications as $doc)
                    <li>{!! $doc->representation() !!}</li>
                @endforeach
            </ul>
            <a href="{{ $base->action('index', ['f0' => 'categories', 'v0' => strval($record)])  }}">Vis som tabellvisning</a>
        </div>
    @endif

@endsection
