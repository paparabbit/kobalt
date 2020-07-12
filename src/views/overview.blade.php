@extends('kobalt::admin_template')

@section('content')

    <div class="container">

        @include('kobalt::partials.breadcrumb', [
            'depth' => 1,
            'title' => $title
        ])

        <div class="pb-2 mt-4 mb-4 border-bottom">
            @if (!isset($addable))
                <a href="{{ $create_path }}" class="btn btn-primary float-right">Add a new {{ $title }}</a>
            @endif

            <h1>List {{ Str::plural( $title) }}</h1>
        </div>

        {{-- Allow a message block of html to be passed to the page --}}

        @if(isset($message))
            {!! $message !!}
        @endif

        {{-- Build up the table --}}

        @if ($data->count())

            <div id='overviewlist'>
                <overview-list
                    meta = "{{ json_encode($meta) }}"
                    list = "{{ json_encode($data) }}"
                    edit_path = "{{ $edit_path }}"
                    editable = "{{ $editable ?? true }}"
                    showable = "{{ $showable ?? false }}"
                    sort_column = "{{ $sort_column ?? '' }}">
                </overview-list>
            </div>

        @endif

    </div>

@endsection
