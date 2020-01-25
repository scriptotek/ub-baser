@extends('opes.layout')

@section('content')

    <div class="row">
        <div class="col col-auto">
            <h2>
                {{ $record->inv_no }}
            </h2>
        </div>
        <div class="col text-sm-right">
            <a class="btn btn-outline-primary" href="{{ $base->action('show', ['id' => $record->prevRecord() ]) }}">
                « {{ __('messages.previous_record') }}
            </a>
            <a class="btn btn-outline-primary" href="{{ $base->action('show', ['id' => $record->nextRecord() ]) }}">
                {{ __('messages.next_record') }} »
            </a>
        </div>

    </div>

    @if ($record->publications[0]->papyri_info_link)
        <a class="btn btn-link" href="{{ $record->publications[0]->papyri_info_link}}">
            <em class="fa fa-arrow-right"></em>
            View Greek text of this document at papyri.info
        </a>
    @endif

    <div class="row record-view">
        <div class="col-6">
            @if ($record->fullsizefront_r1 || $record->fullsizeback_r1)
                @if ($record->fullsizefront_r1)
                <div>
                    <image-viewer
                        id="ods_front"
                        tile-src="https://ub-media.uio.no/OPES/pyramids/{{ $record->fullsizefront_r1 }}.dzi"
                    ></image-viewer>
                    <a target="_blank" href="https://ub-media.uio.no/OPES/jpg/{{ $record->fullsizefront_r1 }}">Open image in new window</a>
                </div>
                @endif

                @if ($record->fullsizeback_r1)
                    <div>
                        <image-viewer
                            id="ods_back"
                            tile-src="https://ub-media.uio.no/OPES/pyramids/{{ $record->fullsizeback_r1 }}.dzi"
                        ></image-viewer>
                        <a target="_blank" href="https://ub-media.uio.no/OPES/jpg/{{ $record->fullsizeback_r1 }}">Open image in new window</a>
                    </div>
                @endif
            @else
                <div class="alert alert-warning">
                    <em class="fa fa-exclamation-triangle"></em>
                    No scans published for this document yet.
                </div>
            @endif
        </div>
        <div class="col">

            {{--
                <table>

                @foreach(['inv_no', 'title_or_type', 'publ_side'] as $key)
                <tr>
                    <th>
                        {{ __('opes.' . $key) }}:
                    </th>
                    <td>
                        {{ $record->{$key} }}
                    </td>
                </tr>
                @endforeach
            </table>
            --}}


            @foreach ($schema->groups as $group)
                @if ($group->label != 'Images')
                    <h4>{{ $group->label }}</h4>
                    <dl class="row">
                        @foreach ($group->fields as $field)
                            @if ($field->displayable && !empty($record->{$field->key}))
                                <dt class="col-sm-3 text-sm-right">
                                    {{ $field->label }}:
                                </dt>
                                <dd class="col-sm-9">
                                    @if (is_array($record->{$field->key}))
                                        @foreach($record->{$field->key} as $value)
                                            <a href="{{ $base->action('index', ['f0' => $field->key, 'v0' => $value]) }}" class="badge badge-primary">{{ $value }}</a>
                                        @endforeach
                                    @else
                                        {{ $record->{$field->key} }}
                                    @endif
                                </dd>
                            @endif
                        @endforeach
                    </dl>
                @endif
            @endforeach

            <h4>Publications</h4>
            <ul class="list-group">
                @foreach ($record->publications as $publication)
                    <li class="list-group-item">
                        <dl class="row">
                            @foreach (['preferred_citation', 'corrections'] as $key)
                                @if (isset($publication->{$key}))
                                    <dt class="col-sm-3 text-sm-right">{{ __('opes.' . $key) }}:</dt>
                                    <dd class="col-sm-9">
                                        {{ $publication->{$key} }}
                                    </dd>
                                @endif
                            @endforeach
                        </dl>
                        @if ($publication->papyri_info_link)
                            <a class="btn btn-link" href="{{ $publication->papyri_info_link }}">
                                <em class="fa fa-arrow-right"></em>
                                View record at papyri.info
                            </a>
                        @endif
                    </li>
                @endforeach
            </ul>

        </div>
    </div>

@endsection

