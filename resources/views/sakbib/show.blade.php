@extends('base.show')

@section('content')

<h2>
    Post {{ $record->id }}
</h2>
@if ($record->trashed())
<div class="alert alert-danger">
    {{ __('base.status.recordtrashed') }}
</div>
@endif
<div class="pb-3">

    <a href="{{ URL::previous() }}" class="btn btn-outline-primary">
        <em class="fa fa-arrow-circle-left"></em>
        Tilbake
    </a>

    @can('letras')

    <a href="{{ $base->action('edit', $record->id) }}" class="btn btn-outline-primary">
        <em class="fa fa-edit"></em>
        Rediger post
    </a>

    @if ($record->trashed())
    <form style="display: inline-block" action="{{ $base->action('restore', $record->id) }}" method="post">
        @csrf
        <button type="submit" class="btn btn-outline-danger btn-xs">
            <em class="fa fa-undo"></em>
            Gjenopprett
        </button>
    </form>
    @else
    <form style="display: inline-block" action="{{ $base->action('destroy', $record->id) }}" method="post">
        @csrf
        <input type="hidden" name="_method" value="DELETE">
        <button type="submit" class="btn btn-outline-danger btn-xs">
            <em class="fa fa-trash"></em>
            Slett
        </button>
    </form>
    @endif
    @endcan
</div>

<dl class="row">
    @foreach ($schema->fields as $field)
        @if ($field->showInRecordView)
        <dt class="col-sm-3 text-sm-right">
            {{ $field->label }}
        </dt>
        <dd class="col-sm-9">
            @if ($field->key == 'creators')
                @foreach ($record->{$field->modelAttribute} as $entity)
                    <a href="{{ $base->action('CreatorController@show', $entity->id) }}">{{ strval($entity) }}</a>
                    {{ ' (' . implode(', ', $entity->pivot->role) . ')' }}<br>
                @endforeach

            @elseif ($field->key == 'categories')
                @foreach ($record->{$field->modelAttribute} as $entity)
                    <a href="{{ $base->action('CategoryController@show', $entity->id) }}">{{ strval($entity) }}</a><br>
                @endforeach

            @elseif (method_exists($field, 'formatValue'))

                {!! $field->formatValue($record->{$field->key}, $base) !!}

            @else
                {{ $record->{$field->key} }}
            @endif
        </dd>
        @endif
    @endforeach
</dl>

@endsection
