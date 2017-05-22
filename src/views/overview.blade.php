@extends('admin.admin_template')

@section('content')

    <div class="container">

        @include('admin.partials.breadcrumb', [
            'depth' => 1,
            'title' => $title
        ])

        <div class="page-header">
            <a href="{{ $create_path }}" class="btn btn-primary pull-right">Add a new {{ $title }}</a>
            <h1>List {{ str_plural( $title) }}</h1>
        </div>


        @if ($data->count())

            <div id='overviewlist'>
                <overview-list
                    meta = "{{ json_encode($meta) }}"
                    list = "{{ json_encode($data) }}"
                    edit_path = "{{ $edit_path }}"
                    sort_column = "{{ $sort_column or '' }}">
                </overview-list>
            </div>

        @endif

    </div>

@endsection
