@extends('kobalt::admin_template')

@section('content')

    <div class="container delete">

        @include('kobalt::partials.breadcrumb', [
            'depth' => 2,
            'button_text' => 'Delete'
        ])

        <div id="lb_content">

            @include('kobalt::partials.page_header', ['title_text' => 'Delete'])

            <div class="row">
            <div class="col-md-12 ">
                <p>Are you sure you want to delete this {{ $title }}?</p>
            </div>
                </div>

            <div class="row form-buttons">
                <div class="col-sm-12">
                    <a href="{{ $back_link }}" class="btn btn-info" id="back_button">Back</a>
                    <a href="#" class="btn btn-danger" id="delete_button">Delete</a>
                </div>
            </div>

            {!! form($delete_form) !!}
        </div>
    </div>

@endsection