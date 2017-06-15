@extends('kobalt::admin_template')

@section('scripts')

    <script>
        $(function(){
            parent.$.magnificPopup.close();
        });
    </script>

@endsection