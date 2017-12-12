@extends('kobalt::admin_template')

@section('content')

    <div class="container">

        @include('kobalt::partials.breadcrumb', [
            'depth' => 2,
            'button_text' => 'Show'
        ])

        <div id="lb_content">

            @include('kobalt::partials.page_header', ['title_text' => 'Show'])

            {!! form($show_form) !!}

            <div class="row form-buttons">
                <div class="col-sm-12">
                    <a href="{{ $back_link }}" class="btn btn-info" id="back_button">Back</a>

                    @if (isset($delete_link))
                        <a href="{{ $delete_link }}" class="btn btn-danger">Delete</a>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection