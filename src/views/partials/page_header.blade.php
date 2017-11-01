<div class="page-header">
    <h1> {{ $title_text. ' ' .$title  }}</h1>
</div>

@include('flash::message')

{{-- Allow a message block of html to be passed to the page --}}

@if(isset($message))
    {!! $message !!}
@endif
