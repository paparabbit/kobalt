<ol class="breadcrumb">
    @if ($depth == 1)
        <li class="active">List {{ str_plural($title) }}</li>
    @else
        @if($back_link != null)
            <li>
                <a href="{{ $back_link }}">Back to {{  $back_title != '' ? str_plural($back_title) : str_plural($title) }}</a>
            </li>
        @endif
        <li class="active">{{ $button_text. ' ' .$title }}</li>
    @endif
</ol>