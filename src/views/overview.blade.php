@extends('kobalt::admin_template')

@section('content')

    <div class="container">

        @include('kobalt::partials.breadcrumb', [
            'depth' => 1,
            'title' => $title
        ])

        <div class="page-header">
            @if (!isset($addable))
                <a href="{{ $create_path }}" class="btn btn-primary pull-right">Add a new {{ $title }}</a>
            @endif

            <h1>List {{ str_plural( $title) }}</h1>
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
                    editable = "{{ $editable or true }}"
                    sort_column = "{{ $sort_column or '' }}">
                </overview-list>
            </div>

        @endif

    </div>

@endsection
