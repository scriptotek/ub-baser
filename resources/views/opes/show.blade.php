@extends('opes.layout')

@section('content')

    <h2>
        {{ $record->inv_no }}
    </h2>

    @if ($record->trashed())
        <div class="alert alert-danger">
            {{ __('base.status.recordtrashed') }}
        </div>
    @endif
    @can('opes')

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

    @foreach ($schema->groups as $group)
        <h4 class="mt-4">{{ $group->label }}</h4>
        <dl class="row">
            @foreach ($group->fields as $field)
                @if ($field->displayable)
                    <dt class="col-sm-3 text-sm-right">
                        {{ $field->label }}
                    </dt>
                    <dd class="col-sm-9">
                        {{ $record->{$field->key} }}
                    </dd>
                @endif
            @endforeach
        </dl>
    @endforeach
@endsection
