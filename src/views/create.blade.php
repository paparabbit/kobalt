@extends('kobalt::admin_template')

@section('content')

    <div class="container">

        @include('kobalt::partials.breadcrumb', [
            'depth' => 2,
            'button_text' => 'Add'
        ])

        <div id="lb_content">

            @include('kobalt::partials.page_header', ['title_text' => 'Add'])

            {!! form_start($create_form) !!}
            {!! form_until($create_form, 'save_field', false, false) !!}

            <div class="row form-buttons">
                <div class="col-sm-12">
                    {!! form_rest($create_form) !!}
                    <a href="{{ $back_link }}" class="btn btn-info" id="back_button">Back</a>
                </div>
            </div>

            {!! form_end($create_form) !!}

        </div>
    </div>

@endsection