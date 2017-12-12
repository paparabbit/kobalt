@extends('kobalt::admin_template')

@section('content')

    <div class="container">

        @include('kobalt::partials.breadcrumb', [
            'depth' => 2,
            'button_text' => 'Show'
        ])

        <div id="lb_content">

            @include('kobalt::partials.page_header', ['title_text' => 'Show'])

            {!! form_start($show_form) !!}
            {{--{!! form_until($edit_form, 'save_field') !!}--}}

            {{--!TODO Need to override the form until method to fix this properly--}}

            <div class="row form-buttons">
                <div class="col-sm-12">
                    {!! form_rest($show_form) !!}
                    <a href="{{ $back_link }}" class="btn btn-info" id="back_button">Back</a>

                    @if (isset($delete_link))
                        <a href="{{ $delete_link }}" class="btn btn-danger">Delete</a>
                    @endif
                </div>
            </div>

            {!! form_end($show_form) !!}


        </div>
    </div>
@endsection