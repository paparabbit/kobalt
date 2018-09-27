<div class="pb-2 mt-4 mb-2 border-bottom">
    <h1> {{ $title_text. ' ' .$title  }}</h1>
</div>

@include('flash::message')

{{-- Allow a message block of html to be passed to the page --}}

@if(isset($message))
    {!! $message !!}
@endif
