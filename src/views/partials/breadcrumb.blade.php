<ol class="breadcrumb">
    @if ($depth == 1)
        <li class="active">List {{ Str::plural($title) }}</li>
    @else
        @if($back_link != null)
            <li>
                <a href="{{ $back_link }}">Back to {{  $back_title != '' ? Str::plural($back_title) : Str::plural($title) }}</a>
            </li>
        @endif
        <li class="active">{{ $button_text. ' ' .$title }}</li>
    @endif
</ol>