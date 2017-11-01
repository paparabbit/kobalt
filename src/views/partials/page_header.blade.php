<div class="page-header">
    <h1> {{ $title_text. ' ' .$title  }}</h1>
</div>

@include('flash::message')

{{-- Allow a message to be passed to the page --}}

@if(isset($message))
    <div class="alert alert-warning alert-important" role="warning">{{ $message }}</div>
@endif
